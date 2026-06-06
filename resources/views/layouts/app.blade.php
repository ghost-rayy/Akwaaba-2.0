<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} @hasSection('title') — @yield('title') @endif</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <livewire:layout.navigation />

            @if (isset($header))
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global Top Loading Progress Bar -->
        <div x-data="{ loading: false }"
             x-on:livewire:loading.window="loading = true"
             x-on:livewire:target-finished.window="loading = false"
             x-on:livewire:navigating.window="loading = true"
             x-on:livewire:navigated.window="loading = false"
             x-show="loading"
             class="fixed top-0 left-0 right-0 h-1 z-[100] bg-stormy-100 overflow-hidden pointer-events-none transition-all duration-300"
             x-cloak>
            <div class="h-full bg-gradient-to-r from-stormy-600 via-emerald-500 to-stormy-700 w-full progress-bar-indeterminate"></div>
        </div>

        <!-- Global Confirm Modal -->
        <div x-data="{ 
                open: false, 
                title: 'Confirm Action', 
                message: '', 
                confirmCallback: null, 
                cancelCallback: null,
                init() {
                    window.showConfirmModal = (msg, onConfirm, onCancel, customTitle = 'Confirm Action') => {
                        this.message = msg;
                        this.title = customTitle;
                        this.confirmCallback = onConfirm;
                        this.cancelCallback = onCancel;
                        this.open = true;
                    };
                },
                confirm() {
                    if (this.confirmCallback) this.confirmCallback();
                    this.open = false;
                },
                cancel() {
                    if (this.cancelCallback) this.cancelCallback();
                    this.open = false;
                }
             }"
             x-show="open"
             x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all duration-300"
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-10 h-10 bg-stormy-50 text-stormy-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" x-text="title">Confirm Action</h3>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-6 leading-relaxed" x-text="message"></p>
                <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" @click="cancel"
                            class="px-4.5 py-2 text-sm font-semibold text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 border border-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="confirm"
                            class="px-4.5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 rounded-xl shadow-md shadow-stormy-600/10 transition-all">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <!-- Global Top Right Toast Notifications -->
        <div x-data="{
                toasts: [],
                add(message, type = 'success') {
                    const id = Date.now() + Math.random().toString(36).substr(2, 9);
                    this.toasts.push({ id, message, type, leaving: false });
                    setTimeout(() => this.remove(id), 5000);
                },
                remove(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.leaving = true;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 350);
                    }
                },
                init() {
                    window.toast = (message, type = 'success') => {
                        this.add(message, type);
                    };
                    
                    window.addEventListener('toast', event => {
                        this.add(event.detail.message || event.detail[0]?.message || '', event.detail.type || event.detail[0]?.type || 'success');
                    });
                }
             }"
             class="fixed top-4 right-4 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
            
            <template x-for="toast in toasts" :key="toast.id">
                <div class="pointer-events-auto w-full rounded-2xl shadow-xl border bg-white/95 backdrop-blur-md p-4 flex gap-3 transform transition-all duration-350"
                     x-bind:class="{
                        'animate-slide-in': !toast.leaving,
                        'animate-slide-out': toast.leaving,
                        'border-emerald-100 text-emerald-800': toast.type === 'success',
                        'border-rose-100 text-rose-800': toast.type === 'error',
                        'border-amber-100 text-amber-800': toast.type === 'warning'
                     }">
                    
                    <div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-xl"
                         x-bind:class="{
                            'bg-emerald-50 text-emerald-600': toast.type === 'success',
                            'bg-rose-50 text-rose-600': toast.type === 'error',
                            'bg-amber-50 text-amber-600': toast.type === 'warning'
                         }">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </template>
                    </div>

                    <div class="flex-1 flex flex-col justify-center">
                        <p class="text-sm font-semibold text-gray-900" x-text="toast.type === 'success' ? 'Success' : (toast.type === 'error' ? 'Error' : 'Warning')"></p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed" x-text="toast.message"></p>
                    </div>

                    <button @click="remove(toast.id)" class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-1 self-start">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('morph.added', ({ el }) => scanAndShowToasts(el));
                scanAndShowToasts(document);

                Livewire.confirm((message, accept, reject) => {
                    window.showConfirmModal(message, accept, reject);
                });
            });

            function scanAndShowToasts(scope) {
                const els = Array.from(scope instanceof Element ? scope.querySelectorAll('.alert-dismiss') : document.querySelectorAll('.alert-dismiss'));
                if (scope instanceof Element && scope.classList.contains('alert-dismiss')) {
                    els.push(scope);
                }
                els.forEach(el => {
                    if (el._ad) return;
                    el._ad = true;
                    
                    const message = el.textContent.trim();
                    if (message) {
                        let type = 'success';
                        if (el.classList.contains('bg-rose-50') || el.classList.contains('bg-red-100') || el.classList.contains('text-rose-700')) {
                            type = 'error';
                        } else if (el.classList.contains('bg-amber-50') || el.classList.contains('bg-yellow-50')) {
                            type = 'warning';
                        }
                        
                        // Wait a microtask to ensure toast system has initialized
                        setTimeout(() => {
                            if (window.toast) {
                                window.toast(message, type);
                            }
                        }, 50);
                    }
                    el.style.display = 'none';
                    el.remove();
                });
            }
        </script>
    </body>
</html>
