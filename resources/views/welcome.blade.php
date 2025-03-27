<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rumeli Türkleri Derneği Bursiyeri</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            primary: {
                                DEFAULT: '#10b981',
                                50: '#ecfdf5',
                                100: '#d1fae5',
                                200: '#a7f3d0',
                                300: '#6ee7b7',
                                400: '#34d399',
                                500: '#10b981',
                                600: '#059669',
                                700: '#047857',
                                800: '#065f46',
                                900: '#064e3b',
                                950: '#022c22',
                            },
                            border: "hsl(var(--border))",
                            input: "hsl(var(--input))",
                            ring: "hsl(var(--ring))",
                            background: "hsl(var(--background))",
                            foreground: "hsl(var(--foreground))",
                            secondary: {
                                DEFAULT: "hsl(var(--secondary))",
                                foreground: "hsl(var(--secondary-foreground))",
                            },
                            muted: {
                                DEFAULT: "hsl(var(--muted))",
                                foreground: "hsl(var(--muted-foreground))",
                            },
                            accent: {
                                DEFAULT: "hsl(var(--accent))",
                                foreground: "hsl(var(--accent-foreground))",
                            },
                            destructive: {
                                DEFAULT: "hsl(var(--destructive))",
                                foreground: "hsl(var(--destructive-foreground))",
                            },
                            card: {
                                DEFAULT: "hsl(var(--card))",
                                foreground: "hsl(var(--card-foreground))",
                            }
                        },
                        borderRadius: {
                            lg: "var(--radius)",
                            md: "calc(var(--radius) - 2px)",
                            sm: "calc(var(--radius) - 4px)",
                        },
                    },
                }
            }
        </script>
        <style>
            :root {
                --background: 0 0% 100%;
                --foreground: 222.2 84% 4.9%;
                --card: 0 0% 100%;
                --card-foreground: 222.2 84% 4.9%;
                --popover: 0 0% 100%;
                --popover-foreground: 222.2 84% 4.9%;
                --primary: 142.1 76.2% 36.3%;
                --primary-foreground: 355.7 100% 97.3%;
                --secondary: 220 14.3% 95.9%;
                --secondary-foreground: 220.9 39.3% 11%;
                --muted: 220 14.3% 95.9%;
                --muted-foreground: 220 8.9% 46.1%;
                --accent: 220 14.3% 95.9%;
                --accent-foreground: 220.9 39.3% 11%;
                --destructive: 0 84.2% 60.2%;
                --destructive-foreground: 210 20% 98%;
                --border: 220 13% 91%;
                --input: 220 13% 91%;
                --ring: 142.1 76.2% 36.3%;
                --radius: 0.5rem;
            }

            .dark {
                --background: 222.2 84% 4.9%;
                --foreground: 210 40% 98%;
                --card: 222.2 84% 4.9%;
                --card-foreground: 210 40% 98%;
                --popover: 222.2 84% 4.9%;
                --popover-foreground: 210 40% 98%;
                --primary: 142.1 70.6% 45.3%;
                --primary-foreground: 144.9 80.4% 10%;
                --secondary: 217.2 32.6% 17.5%;
                --secondary-foreground: 210 40% 98%;
                --muted: 217.2 32.6% 17.5%;
                --muted-foreground: 215 20.2% 65.1%;
                --accent: 217.2 32.6% 17.5%;
                --accent-foreground: 210 40% 98%;
                --destructive: 0 62.8% 30.6%;
                --destructive-foreground: 210 40% 98%;
                --border: 217.2 32.6% 17.5%;
                --input: 217.2 32.6% 17.5%;
                --ring: 142.4 71.8% 29.2%;
            }

            * {
                border-color: hsl(var(--border));
            }

            body {
                background-color: hsl(var(--background));
                color: hsl(var(--foreground));
                font-feature-settings: "rlig" 1, "calt" 1;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: var(--radius);
                font-weight: 500;
                transition: all 0.2s ease;
                padding: 0.5rem 1rem;
                height: 2.5rem;
            }

            .btn-primary {
                background-color: hsl(var(--primary));
                color: hsl(var(--primary-foreground));
            }

            .btn-primary:hover {
                background-color: hsl(var(--primary) / 0.9);
            }

            .btn-secondary {
                background-color: hsl(var(--secondary));
                color: hsl(var(--secondary-foreground));
            }

            .btn-secondary:hover {
                background-color: hsl(var(--secondary) / 0.9);
            }

            .btn-outline {
                border: 1px solid hsl(var(--border));
                background-color: transparent;
                color: hsl(var(--foreground));
            }

            .btn-outline:hover {
                background-color: hsl(var(--accent));
                color: hsl(var(--accent-foreground));
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="border-b border-gray-200 dark:border-gray-800">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center">
                            <a href="/" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary-600">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                                <span class="font-bold text-xl">Rumeli Türkleri Derneği</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex gap-2">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ url('user/login') }}" class="btn btn-outline">
                                        Giriş Yap
                                    </a>
                                    <a href="{{ url('user/register') }}" class="btn btn-primary">
                                        Hesap Oluştur
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
                <div class="container mx-auto px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-2">
                        <div class="flex flex-col justify-center">
                            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                                Bursiyerlik Programına <span class="text-primary-600">Hoş Geldiniz</span>
                            </h1>
                            <p class="mt-6 text-lg text-gray-600 dark:text-gray-300 max-w-md">
                                Rumeli Türkleri Derneği bursiyerlik programı ile eğitiminize destek olmak için buradayız.
                            </p>
                            <div class="mt-10 flex items-center gap-x-6">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-primary px-5 py-3">
                                        Dashboard'a Git
                                    </a>
                                @else
                                    <a href="{{ url('user/register') }}" class="btn btn-primary px-5 py-3">
                                        Şimdi Başvur
                                    </a>
                                    <a href="{{ url('user/login') }}" class="btn btn-outline">
                                        Giriş Yap
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div class="relative lg:pl-10">
                            <div class="relative overflow-hidden rounded-xl bg-white shadow-xl dark:bg-gray-800">
                                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" 
                                     alt="Bursiyerlik" 
                                     class="w-full h-auto object-cover aspect-[4/3]">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-12 sm:py-16">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto text-center mb-12">
                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Programımızın Avantajları</h2>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                            Bursiyerlere sunduğumuz imkanlar ve fırsatlar
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="flex flex-col items-start p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="rounded-md bg-primary-50 dark:bg-primary-900/20 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary-600">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium">Eğitim Bursları</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Öğrencilerin eğitim masraflarını karşılamak için finansal destek sağlıyoruz.
                            </p>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="flex flex-col items-start p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="rounded-md bg-primary-50 dark:bg-primary-900/20 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary-600">
                                    <path d="m18 16 4-4-4-4"></path>
                                    <path d="m6 8-4 4 4 4"></path>
                                    <path d="m14.5 4-5 16"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium">Mentorluk</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Deneyimli profesyonellerden mentorluk desteği ile kariyer gelişiminize katkı sağlıyoruz.
                            </p>
                        </div>
                        
                        <!-- Feature 3 -->
                        <div class="flex flex-col items-start p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="rounded-md bg-primary-50 dark:bg-primary-900/20 p-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary-600">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium">Topluluk</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Çeşitli etkinliklerle bağlantı kurma ve networking fırsatları sunuyoruz.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="bg-primary-50 dark:bg-primary-900/10 py-12 sm:py-16">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Hemen Başvurun</h2>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                            Bursiyerlik programımız için başvurular açıldı. Son başvuru tarihini kaçırmamak için hemen kayıt olun.
                        </p>
                        <div class="mt-8 flex justify-center gap-x-4">
                            <a href="{{ url('user/register') }}" class="btn btn-primary px-5 py-3">
                                Hesap Oluştur
                            </a>
                            <a href="{{ url('user/login') }}" class="btn btn-outline">
                                Giriş Yap
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="mt-auto border-t border-gray-200 dark:border-gray-800 py-8">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col items-center justify-center gap-4 md:flex-row md:justify-between">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-primary-600">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <span class="font-medium">Rumeli Türkleri Derneği &copy; {{ date('Y') }}</span>
                        </div>
                        <div class="flex gap-4">
                            <a href="#" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-500">
                                Hakkımızda
                            </a>
                            <a href="#" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-500">
                                İletişim
                            </a>
                            <a href="#" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-500">
                                Gizlilik Politikası
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>