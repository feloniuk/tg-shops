<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Create Your Telegram Shop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">TG Shops</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/shops') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                            My Shops
                        </a>
                        <a href="{{ url('/shops') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-white pt-16 pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl font-extrabold text-gray-900 sm:text-6xl">
                    Create Your <span class="text-indigo-600">Telegram Shop</span> in Minutes
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-500">
                    Build and manage your online store directly in Telegram. No coding required. Start selling products to millions of Telegram users today.
                </p>
                <div class="mt-10 flex justify-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                            Start Free Trial
                        </a>
                    @endif
                    <a href="#features" class="px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Everything You Need to Sell on Telegram
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                    Powerful features to manage your online business
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Product Catalog</h3>
                    <p class="mt-2 text-gray-600">Create unlimited products with categories, descriptions, and prices. Showcase your entire inventory beautifully.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Smart Shopping Cart</h3>
                    <p class="mt-2 text-gray-600">Built-in cart system for your Telegram bot. Customers can easily browse and order products.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Order Management</h3>
                    <p class="mt-2 text-gray-600">Track all orders in real-time. Update statuses, view customer details, and manage your sales efficiently.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">AI-Powered Descriptions</h3>
                    <p class="mt-2 text-gray-600">Generate compelling product descriptions automatically using AI. Save time and boost sales.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Payment Integration</h3>
                    <p class="mt-2 text-gray-600">Accept payments seamlessly with Stripe integration. Secure and reliable payment processing.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Multi-Language</h3>
                    <p class="mt-2 text-gray-600">Support for multiple languages. Reach customers worldwide with English and Ukrainian support.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Simple, Transparent Pricing
                </h2>
                <p class="mt-4 text-xl text-gray-500">
                    Choose the perfect plan for your business
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Free Plan -->
                <div class="border border-gray-200 rounded-lg p-8 bg-white">
                    <h3 class="text-2xl font-semibold text-gray-900">Free</h3>
                    <p class="mt-4 text-gray-500">Perfect for getting started</p>
                    <p class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900">$0</span>
                        <span class="text-gray-500">/month</span>
                    </p>
                    <ul class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">1 Shop</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Up to 50 products</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Basic support</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full bg-gray-100 text-gray-900 text-center px-4 py-3 rounded-md font-medium hover:bg-gray-200">
                        Get Started
                    </a>
                </div>

                <!-- Base Plan -->
                <div class="border border-gray-200 rounded-lg p-8 bg-white">
                    <h3 class="text-2xl font-semibold text-gray-900">Base</h3>
                    <p class="mt-4 text-gray-500">For growing businesses</p>
                    <p class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900">$19</span>
                        <span class="text-gray-500">/month</span>
                    </p>
                    <ul class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">3 Shops</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Up to 500 products</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">AI descriptions</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Priority support</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full bg-indigo-600 text-white text-center px-4 py-3 rounded-md font-medium hover:bg-indigo-700">
                        Get Started
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="border-2 border-indigo-600 rounded-lg p-8 bg-white relative">
                    <div class="absolute top-0 right-0 -mt-4 mr-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            Popular
                        </span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900">Pro</h3>
                    <p class="mt-4 text-gray-500">For power sellers</p>
                    <p class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900">$49</span>
                        <span class="text-gray-500">/month</span>
                    </p>
                    <ul class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Unlimited shops</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Unlimited products</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Advanced AI features</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Premium support</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="ml-3 text-gray-600">Custom branding</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full bg-indigo-600 text-white text-center px-4 py-3 rounded-md font-medium hover:bg-indigo-700">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready to get started?</span>
                <span class="block text-indigo-200">Create your first shop today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500">
                <p>&copy; {{ date('Y') }} TG Shops. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
