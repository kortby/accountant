<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    title: String,
});

const page = usePage();
const mobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans text-slate-900 flex flex-col">
        
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link href="/" class="flex-shrink-0 flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-600 rounded-md flex items-center justify-center text-white font-bold">
                                T
                            </div>
                            <span class="font-bold text-xl tracking-tight text-slate-900">TaxesAccountant</span>
                        </Link>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <Link href="/" :class="{'border-orange-500 text-slate-900': $page.url === '/', 'border-transparent text-slate-500 hover:border-orange-300 hover:text-orange-600': $page.url !== '/'}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                                Home
                            </Link>
                            <Link href="/tax-services" :class="{'border-orange-500 text-slate-900': $page.url.startsWith('/services'), 'border-transparent text-slate-500 hover:border-orange-300 hover:text-orange-600': !$page.url.startsWith('/services')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                                Services
                            </Link>
                            <Link href="/about" :class="{'border-orange-500 text-slate-900': $page.url.startsWith('/about'), 'border-transparent text-slate-500 hover:border-orange-300 hover:text-orange-600': !$page.url.startsWith('/about')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                                About Us
                            </Link>
                            <Link href="/contact" :class="{'border-orange-500 text-slate-900': $page.url.startsWith('/contact'), 'border-transparent text-slate-500 hover:border-orange-300 hover:text-orange-600': !$page.url.startsWith('/contact')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                                Contact
                            </Link>
                        </div>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="flex items-center gap-4">
                            <Link v-if="$page.props.auth.user" href="/dashboard" class="text-sm font-medium text-slate-700 hover:text-orange-600">
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link href="/login" class="text-sm font-medium text-slate-700 hover:text-orange-600">
                                    Log in
                                </Link>
                                <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-orange-600 text-white hover:bg-orange-700 h-9 px-4 py-2 shadow">
                                    Get Started
                                </Link>
                            </template>
                        </div>
                    </div>

                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="toggleMobileMenu" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="md:hidden bg-white border-t border-slate-200">
                <div class="pt-2 pb-3 space-y-1">
                    <Link href="/" class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 block pl-3 pr-4 py-2 text-base font-medium">Home</Link>
                    <Link href="/services" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Services</Link>
                    <Link href="/about" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">About</Link>
                    <Link href="/contact" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Contact</Link>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-slate-900 mt-auto">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-slate-400">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 bg-orange-600 rounded flex items-center justify-center text-white text-xs font-bold">T</div>
                            <span class="font-bold text-white text-lg">TaxesAccountant</span>
                        </div>
                        <p class="text-sm">Making tax filing simple, secure, and stress-free for everyone.</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200 tracking-wider uppercase">Services</h3>
                        <ul class="mt-4 space-y-4">
                            <li><Link  href="/tax.services" class="text-base hover:text-white">Personal Taxes</Link></li>
                            <li><Link  href="/tax.services" class="text-base hover:text-white">Business Taxes</Link></li>
                            <li><Link  href="/tax.services" class="text-base hover:text-white">Audit Defense</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200 tracking-wider uppercase">Company</h3>
                        <ul class="mt-4 space-y-4">
                            <li><Link href="/about"lass="text-base hover:text-white">About</Link></li>
                            <li><Link href="/contact"lass="text-base hover:text-white">Contact</Link></li>
                            <li><Link href="#" class="text-base hover:text-white">Privacy Policy</Link></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-slate-800 pt-8 flex justify-center">
                    <p class="text-base">&copy; {{ new Date().getFullYear() }} TaxesAccountant. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>