/**
 * Frontend modal functionality with advanced triggers and tracking
 */

( function() {
	'use strict';

	if ( typeof modalBuilderData === 'undefined' || ! modalBuilderData.modals ) {
		return;
	}

	const modals = modalBuilderData.modals;
	const pageContext = modalBuilderData.pageContext || {
		currentPostId: 0,
		isHomepage: false,
		isPage: false,
		isPost: false,
	};
	let activeModal = null;
	let focusedElementBeforeModal = null;
	const STORAGE_PREFIX = 'mb_';

	/**
	 * Local storage helpers
	 */
	function setStorage( key, value, days = 365 ) {
		const expires = new Date();
		expires.setTime( expires.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		try {
			localStorage.setItem( STORAGE_PREFIX + key, JSON.stringify( {
				value,
				expires: expires.getTime(),
			} ) );
		} catch ( e ) {
			// Fallback to cookies if localStorage unavailable
			document.cookie = STORAGE_PREFIX + key + '=' + JSON.stringify( value ) + ';expires=' + expires.toUTCString() + ';path=/';
		}
	}

	function getStorage( key ) {
		try {
			const item = localStorage.getItem( STORAGE_PREFIX + key );
			if ( ! item ) return null;
			const data = JSON.parse( item );
			if ( data.expires && Date.now() > data.expires ) {
				localStorage.removeItem( STORAGE_PREFIX + key );
				return null;
			}
			return data.value;
		} catch ( e ) {
			return null;
		}
	}

	/**
	 * Session management
	 */
	function getSessionId() {
		let sessionId = sessionStorage.getItem( STORAGE_PREFIX + 'session_id' );
		if ( ! sessionId ) {
			sessionId = Date.now() + '_' + Math.random().toString( 36 ).substr( 2, 9 );
			sessionStorage.setItem( STORAGE_PREFIX + 'session_id', sessionId );
		}
		return sessionId;
	}

	function getSessionCount() {
		const sessions = getStorage( 'sessions' ) || [];
		return sessions.length;
	}

	function incrementSessionCount() {
		const sessionId = getSessionId();
		const sessions = getStorage( 'sessions' ) || [];
		if ( ! sessions.includes( sessionId ) ) {
			sessions.push( sessionId );
			setStorage( 'sessions', sessions, 365 );
		}
	}

	incrementSessionCount();

	/**
	 * Page view tracking
	 */
	function getPageViewCount() {
		const sessionId = getSessionId();
		const key = 'pageviews_' + sessionId;
		return parseInt( sessionStorage.getItem( key ) || '0', 10 );
	}

	function incrementPageViewCount() {
		const sessionId = getSessionId();
		const key = 'pageviews_' + sessionId;
		const count = getPageViewCount() + 1;
		sessionStorage.setItem( key, count.toString() );
	}

	incrementPageViewCount();

	/**
	 * Browser detection
	 */
	function detectBrowser() {
		const ua = navigator.userAgent;
		if ( ua.indexOf( 'Chrome' ) > -1 && ua.indexOf( 'Edg' ) === -1 ) return 'chrome';
		if ( ua.indexOf( 'Safari' ) > -1 && ua.indexOf( 'Chrome' ) === -1 ) return 'safari';
		if ( ua.indexOf( 'Firefox' ) > -1 ) return 'firefox';
		if ( ua.indexOf( 'Edg' ) > -1 ) return 'edge';
		return 'other';
	}

	/**
	 * Check frequency limits
	 */
	function checkFrequencyLimit( modalId, limit, period ) {
		if ( limit === 0 ) return true;

		const key = 'frequency_' + modalId;
		const data = getStorage( key ) || { count: 0, period, timestamp: Date.now() };

		const periodMap = {
			session: 0,
			day: 24 * 60 * 60 * 1000,
			week: 7 * 24 * 60 * 60 * 1000,
			month: 30 * 24 * 60 * 60 * 1000,
			lifetime: Infinity,
		};

		const timeSince = Date.now() - data.timestamp;
		const periodDuration = periodMap[ period ];

		if ( period === 'session' ) {
			const sessionKey = 'frequency_session_' + modalId;
			const sessionCount = parseInt( sessionStorage.getItem( sessionKey ) || '0', 10 );
			return sessionCount < limit;
		}

		if ( periodDuration !== Infinity && timeSince > periodDuration ) {
			setStorage( key, { count: 0, period, timestamp: Date.now() } );
			return true;
		}

		return data.count < limit;
	}

	function incrementFrequencyCount( modalId, period ) {
		const key = 'frequency_' + modalId;
		const data = getStorage( key ) || { count: 0, period, timestamp: Date.now() };
		data.count++;
		setStorage( key, data );

		if ( period === 'session' ) {
			const sessionKey = 'frequency_session_' + modalId;
			const sessionCount = parseInt( sessionStorage.getItem( sessionKey ) || '0', 10 );
			sessionStorage.setItem( sessionKey, ( sessionCount + 1 ).toString() );
		}
	}

	/**
	 * Check if modal should show based on all rules
	 */
	function shouldShowModal( modal ) {
		// Check page/post targeting rules first
		const targeting = modal.pageTargeting || 'entire_site';

		switch ( targeting ) {
			case 'entire_site':
				// Show everywhere - no restrictions
				break;

			case 'homepage_only':
				if ( ! pageContext.isHomepage ) {
					return false;
				}
				break;

			case 'posts_only':
				if ( ! pageContext.isPost ) {
					return false;
				}
				break;

			case 'pages_only':
				if ( ! pageContext.isPage ) {
					return false;
				}
				break;

			case 'selected_posts_pages':
				if ( modal.targetPostsPages ) {
					const targetIds = modal.targetPostsPages.split( ',' ).map( id => parseInt( id.trim() ) ).filter( id => id > 0 );
					if ( targetIds.length > 0 ) {
						// Only show on selected posts/pages
						if ( ! targetIds.includes( pageContext.currentPostId ) ) {
							return false;
						}
					} else {
						// No valid IDs selected, don't show
						return false;
					}
				} else {
					// No posts/pages selected, don't show
					return false;
				}
				break;
		}

		// Check page views threshold
		if ( modal.pageViewsThreshold > 0 && getPageViewCount() < modal.pageViewsThreshold ) {
			return false;
		}

		// Check session threshold
		if ( modal.sessionThreshold > 0 && getSessionCount() < modal.sessionThreshold ) {
			return false;
		}

		// Check frequency limit
		if ( modal.frequencyLimit > 0 && ! checkFrequencyLimit( modal.id, modal.frequencyLimit, modal.frequencyPeriod ) ) {
			return false;
		}

		// Check browser targeting
		if ( modal.browsers !== 'all' && detectBrowser() !== modal.browsers ) {
			return false;
		}

		// Check referrer filter
		if ( modal.referrerFilter ) {
			const referrer = document.referrer;
			if ( ! referrer || referrer.indexOf( modal.referrerFilter ) === -1 ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Create modal HTML with position and animation
	 */
	function createModal( modal ) {
		const overlay = document.createElement( 'div' );
		overlay.className = 'easy-pop-overlay';
		overlay.setAttribute( 'role', 'dialog' );
		overlay.setAttribute( 'aria-modal', 'true' );
		overlay.setAttribute( 'aria-labelledby', 'easy-pop-' + modal.id );
		overlay.setAttribute( 'data-modal-id', modal.id );
		overlay.setAttribute( 'data-size', modal.size || 'default' );
		overlay.setAttribute( 'data-position', modal.position );
		overlay.setAttribute( 'data-animation', modal.animation );
		overlay.style.backgroundColor = 'rgba(0, 0, 0, ' + ( modal.overlayOpacity / 100 ) + ')';

		if ( modal.devices !== 'all' ) {
			overlay.setAttribute( 'data-devices', modal.devices );
		}

		const container = document.createElement( 'div' );
		container.className = 'easy-pop-container';

		if ( modal.showCloseButton ) {
			const closeButton = document.createElement( 'button' );
			closeButton.className = 'easy-pop-close';
			closeButton.setAttribute( 'aria-label', 'Close modal' );
			closeButton.setAttribute( 'type', 'button' );
			container.appendChild( closeButton );
		}

		const content = document.createElement( 'div' );
		content.className = 'easy-pop-content';
		content.id = 'easy-pop-' + modal.id;
		content.innerHTML = modal.content;

		container.appendChild( content );
		overlay.appendChild( container );

		return overlay;
	}

	/**
	 * Show modal
	 */
	function showModal( modal ) {
		if ( ! shouldShowModal( modal ) ) {
			return;
		}

		const modalElement = createModal( modal );
		document.body.appendChild( modalElement );

		focusedElementBeforeModal = document.activeElement;
		activeModal = modalElement;

		requestAnimationFrame( () => {
			modalElement.classList.add( 'is-visible' );
		} );

		incrementFrequencyCount( modal.id, modal.frequencyPeriod );

		const closeButton = modalElement.querySelector( '.easy-pop-close' );
		if ( closeButton ) {
			closeButton.addEventListener( 'click', closeModal );
			closeButton.focus();
		}

		if ( modal.backdropClose ) {
			modalElement.addEventListener( 'click', function( e ) {
				if ( e.target === modalElement ) {
					closeModal();
				}
			} );
		}

		trapFocus( modalElement, modal.escClose );
	}

	/**
	 * Close modal
	 */
	function closeModal() {
		if ( ! activeModal ) {
			return;
		}

		activeModal.classList.add( 'is-closing' );

		setTimeout( () => {
			if ( activeModal && activeModal.parentNode ) {
				activeModal.parentNode.removeChild( activeModal );
			}
			activeModal = null;

			if ( focusedElementBeforeModal ) {
				focusedElementBeforeModal.focus();
			}
		}, 300 );
	}

	/**
	 * Trap focus within modal
	 */
	function trapFocus( element, escClose ) {
		const focusableElements = element.querySelectorAll(
			'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
		);

		if ( focusableElements.length === 0 ) {
			return;
		}

		const firstFocusable = focusableElements[ 0 ];
		const lastFocusable = focusableElements[ focusableElements.length - 1 ];

		element.addEventListener( 'keydown', function( e ) {
			if ( e.key === 'Escape' && escClose ) {
				closeModal();
				return;
			}

			if ( e.key !== 'Tab' ) {
				return;
			}

			if ( e.shiftKey ) {
				if ( document.activeElement === firstFocusable ) {
					lastFocusable.focus();
					e.preventDefault();
				}
			} else {
				if ( document.activeElement === lastFocusable ) {
					firstFocusable.focus();
					e.preventDefault();
				}
			}
		} );
	}

	/**
	 * Setup triggers for each modal
	 */
	modals.forEach( function( modal ) {
		switch ( modal.triggerType ) {
			case 'page_load':
				if ( modal.triggerDelay > 0 ) {
					setTimeout( () => showModal( modal ), modal.triggerDelay * 1000 );
				} else {
					showModal( modal );
				}
				break;

			case 'scroll':
				let scrollTriggered = false;
				const checkScroll = function() {
					if ( scrollTriggered ) return;
					const scrollPercentage = ( window.scrollY / ( document.documentElement.scrollHeight - window.innerHeight ) ) * 100;
					if ( scrollPercentage >= modal.triggerScrollPercentage ) {
						scrollTriggered = true;
						showModal( modal );
						window.removeEventListener( 'scroll', checkScroll );
					}
				};
				window.addEventListener( 'scroll', checkScroll );
				break;

			case 'click':
				if ( modal.triggerClickSelector ) {
					document.addEventListener( 'click', function( e ) {
						if ( e.target.matches( modal.triggerClickSelector ) || e.target.closest( modal.triggerClickSelector ) ) {
							e.preventDefault();
							showModal( modal );
						}
					} );
				}
				break;

			case 'exit_intent':
				let exitTriggered = false;
				document.addEventListener( 'mouseout', function( e ) {
					if ( exitTriggered ) return;
					if ( ! e.relatedTarget && e.clientY < 10 ) {
						exitTriggered = true;
						if ( modal.triggerDelay > 0 ) {
							setTimeout( () => showModal( modal ), modal.triggerDelay * 1000 );
						} else {
							showModal( modal );
						}
					}
				} );
				break;

			case 'inactivity':
				let inactivityTimer;
				let inactivityTriggered = false;
				const resetInactivityTimer = function() {
					if ( inactivityTriggered ) return;
					clearTimeout( inactivityTimer );
					inactivityTimer = setTimeout( () => {
						inactivityTriggered = true;
						showModal( modal );
					}, modal.triggerInactivitySeconds * 1000 );
				};
				[ 'mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart' ].forEach( function( event ) {
					document.addEventListener( event, resetInactivityTimer, true );
				} );
				resetInactivityTimer();
				break;

			case 'scroll_element':
				if ( modal.triggerScrollElement ) {
					const observer = new IntersectionObserver( function( entries ) {
						entries.forEach( function( entry ) {
							if ( entry.isIntersecting ) {
								showModal( modal );
								observer.disconnect();
							}
						} );
					}, { threshold: 0.5 } );

					const targetElement = document.querySelector( modal.triggerScrollElement );
					if ( targetElement ) {
						observer.observe( targetElement );
					}
				}
				break;
		}
	} );

} )();