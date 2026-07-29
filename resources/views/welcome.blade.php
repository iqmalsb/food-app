<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>DineFlow | Contactless Dining, Kiosk & KDS System</title>
        <meta name="description" content="DineFlow is a modern contactless waiting and ordering system for restaurants. Scan QR codes to order/pay, deploy self-service kiosks, or run iPad POS terminals.">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    </head>
    <body>
        <!-- Background Glowing Mesh -->
        <div class="bg-glow-container" aria-hidden="true">
            <div class="bg-glow-1"></div>
            <div class="bg-glow-2"></div>
        </div>

        <!-- Navigation Bar -->
        <header>
            <nav class="nav-container" aria-label="Main Navigation">
                <a href="#" class="logo" id="nav-logo">
                    <div class="logo-dot"></div>
                    DineFlow
                </a>
                
                <ul class="nav-links">
                    <li><a href="#how-it-works">How it Works</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#contact">Request Demo</a></li>
                </ul>

                <div class="nav-auth">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="btn-primary nav-btn" id="nav-home-btn">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary" id="nav-login-btn" style="padding: 0.5rem 1.25rem; font-size: 0.9rem; border-radius: 9999px; margin-right: 0.75rem;">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary nav-btn" id="nav-register-btn">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="hero-section" id="hero">
                <div class="hero-content">
                    <span class="section-tag">Next-Gen Dining</span>
                    <h1>Contactless waiting and <span>smart ordering</span> for modern restaurants</h1>
                    <p class="hero-subtitle">
                        Streamline your operations with DineFlow. Allow customers to scan a table QR code to order and pay, deploy self-service kiosks, or empower wait staff with responsive iPad POS terminals.
                    </p>
                    <div class="hero-ctas">
                        <a href="#contact" class="btn-primary" id="hero-cta-demo">
                            Book a Demo
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.25rem;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <a href="#how-it-works" class="btn-secondary" id="hero-cta-explore">Explore Product</a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <h3>35%</h3>
                            <p>Increase in Table Turn</p>
                        </div>
                        <div class="stat-item">
                            <h3>2.5x</h3>
                            <p>Faster Ordering Speed</p>
                        </div>
                        <div class="stat-item">
                            <h3>0%</h3>
                            <p>Staff Shortage Delays</p>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-card">
                        <img src="{{ asset('images/phone_qr_ordering.jpg') }}" alt="Vibrant food ordering app mock-up displayed on a smartphone" width="600" height="450" fetchpriority="high">
                    </div>
                </div>
            </section>

            <!-- How it Works (Tabs Switcher) -->
            <section id="how-it-works" class="tabs-section">
                <div style="text-align: center; margin-bottom: 4rem; display: flex; flex-direction: column; align-items: center;">
                    <span class="section-tag">The DineFlow Ecosystem</span>
                    <h2 class="section-title" style="max-width: 600px;">Three interfaces. One unified engine.</h2>
                    <p class="section-desc">Choose the perfect blend of channels to suit your restaurant style and maximize efficiency.</p>
                </div>

                <div class="tabs-wrapper">
                    <div class="tabs-menu" role="tablist" aria-label="Ordering Channels">
                        <button class="tab-btn active" role="tab" aria-selected="true" aria-controls="panel-qr" id="tab-qr" onclick="switchTab(event, 'panel-qr')">
                            <div class="tab-btn-title">
                                📱 Scan & Order (BYOD)
                            </div>
                            <p class="tab-btn-desc">
                                Customers scan a table QR code to browse your gorgeous menu, customize items, and complete payments securely on their own phones.
                            </p>
                        </button>
                        <button class="tab-btn" role="tab" aria-selected="false" aria-controls="panel-kiosk" id="tab-kiosk" onclick="switchTab(event, 'panel-kiosk')">
                            <div class="tab-btn-title">
                                🖥️ Self-Service Kiosks
                            </div>
                            <p class="tab-btn-desc">
                                High-throughput kiosk stations for walk-ins and express checkouts. Simplifies queues and increases average ticket sizes by 20%.
                            </p>
                        </button>
                        <button class="tab-btn" role="tab" aria-selected="false" aria-controls="panel-ipad" id="tab-ipad" onclick="switchTab(event, 'panel-ipad')">
                            <div class="tab-btn-title">
                                📋 Waiter iPad POS
                            </div>
                            <p class="tab-btn-desc">
                                Put a powerful order-taking interface directly in your waitstaff's hands. Take orders table-side and sync instantly with the kitchen.
                            </p>
                        </button>
                    </div>

                    <div class="tabs-content">
                        <!-- Panel 1: QR -->
                        <div class="tab-panel active" id="panel-qr" role="tabpanel" aria-labelledby="tab-qr">
                            <img class="tab-panel-image" src="{{ asset('images/phone_qr_ordering.jpg') }}" alt="Contactless table-top QR ordering menu mockup displayed on a smartphone" width="600" height="450" loading="lazy">
                        </div>
                        <!-- Panel 2: Kiosk -->
                        <div class="tab-panel" id="panel-kiosk" role="tabpanel" aria-labelledby="tab-kiosk">
                            <img class="tab-panel-image" src="{{ asset('images/food_kiosk_ordering.jpg') }}" alt="Self-service ordering kiosk machine in a fast-casual restaurant" width="600" height="450" loading="lazy">
                        </div>
                        <!-- Panel 3: iPad -->
                        <div class="tab-panel" id="panel-ipad" role="tabpanel" aria-labelledby="tab-ipad">
                            <img class="tab-panel-image" src="{{ asset('images/waiter_ipad_app.jpg') }}" alt="Staff iPad ordering interface held by a waiter" width="600" height="450" loading="lazy">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Grid -->
            <section id="features">
                <div style="text-align: center; margin-bottom: 4rem; display: flex; flex-direction: column; align-items: center;">
                    <span class="section-tag">Key Features</span>
                    <h2 class="section-title">Built to grow your hospitality business</h2>
                    <p class="section-desc">Powerful features designed to increase revenue, reduce errors, and delight your guests.</p>
                </div>

                <div class="features-grid">
                    <!-- Feature 1 -->
                    <div class="feature-card">
                        <div class="feature-icon" aria-hidden="true">⚡</div>
                        <div class="feature-card-content">
                            <h3>Real-time Menu Updates</h3>
                            <p>Instantly update prices, toggle out-of-stock ingredients, or highlight chef specials. Changes reflect across QR codes, kiosks, and iPads in seconds.</p>
                        </div>
                    </div>
                    <!-- Feature 2 -->
                    <div class="feature-card">
                        <div class="feature-icon" aria-hidden="true">💳</div>
                        <div class="feature-card-content">
                            <h3>Secure Instant Checkout</h3>
                            <p>Accept Apple Pay, Google Pay, and major credit cards directly on checkout. Split-bill support makes dining with friends completely frictionless.</p>
                        </div>
                    </div>
                    <!-- Feature 3 -->
                    <div class="feature-card">
                        <div class="feature-icon" aria-hidden="true">🔥</div>
                        <div class="feature-card-content">
                            <h3>Unified Kitchen Display (KDS)</h3>
                            <p>Orders from all channels feed into a single, clean display. Keep your chef and line cooks organized and reduce preparation delays.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact / Request Demo -->
            <section id="contact" class="contact-section">
                <div class="contact-info">
                    <span class="section-tag">Partner with Us</span>
                    <h2>Ready to revolutionize your restaurant?</h2>
                    <p>
                        Fill out the form to request a personalized demo. Let our experts show you how DineFlow can optimize your table turn rate, increase average spending, and solve staffing headaches.
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 1rem; color: var(--text-secondary);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: var(--color-primary); font-size: 1.25rem;">✔</span> Personalized setup & installation
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: var(--color-primary); font-size: 1.25rem;">✔</span> 24/7 dedicated support
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="color: var(--color-primary); font-size: 1.25rem;">✔</span> 14-day free trial on active operations
                        </div>
                    </div>
                </div>

                <div class="contact-card">
                    <form class="contact-form" onsubmit="handleFormSubmit(event)">
                        <div class="form-group">
                            <label for="contact-name">Full Name</label>
                            <input type="text" id="contact-name" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email Address</label>
                            <input type="email" id="contact-email" name="email" required placeholder="john@restaurant.com">
                        </div>
                        <div class="form-group">
                            <label for="contact-restaurant">Restaurant Name</label>
                            <input type="text" id="contact-restaurant" name="restaurant" required placeholder="Savor & Co.">
                        </div>
                        <div class="form-group">
                            <label for="contact-outlets">Number of Outlets</label>
                            <select id="contact-outlets" name="outlets">
                                <option value="1">1 outlet</option>
                                <option value="2-5">2 - 5 outlets</option>
                                <option value="6-10">6 - 10 outlets</option>
                                <option value="10+">10+ outlets</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" id="contact-submit-btn">Request a Demo</button>
                    </form>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer>
            <div class="footer-container">
                <div class="footer-copyright" id="footer-copy">
                    &copy; {{ date('Y') }} DineFlow. All rights reserved.
                </div>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook">🌐</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="LinkedIn">💼</a>
                </div>
            </div>
        </footer>

        <!-- Javascript -->
        <script>
            function switchTab(event, panelId) {
                // Prevent page scroll jumps
                event.preventDefault();

                // Deactivate all buttons
                const buttons = document.querySelectorAll('.tab-btn');
                buttons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });

                // Deactivate all panels
                const panels = document.querySelectorAll('.tab-panel');
                panels.forEach(panel => {
                    panel.classList.remove('active');
                });

                // Activate clicked button
                const clickedBtn = event.currentTarget;
                clickedBtn.classList.add('active');
                clickedBtn.setAttribute('aria-selected', 'true');

                // Activate corresponding panel
                const activePanel = document.getElementById(panelId);
                activePanel.classList.add('active');
            }

            function handleFormSubmit(event) {
                event.preventDefault();
                const name = document.getElementById('contact-name').value;
                const restaurant = document.getElementById('contact-restaurant').value;
                alert(`Thank you, ${name}! Your demo request for "${restaurant}" has been received. Our team will contact you within 24 hours.`);
                event.target.reset();
            }
        </script>
    </body>
</html>
