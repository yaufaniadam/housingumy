<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Housing UMY Login</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;700;900&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#93251f",
                        "background-light": "#f8f6f6",
                        "background-dark": "#201312",
                        "brand-green": "#004029",
                        "brand-yellow": "#F7B800",
                    },
                    fontFamily: {
                        "display": ["Lexend", "Noto Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-[#171212]">
<div class="flex min-h-screen w-full overflow-hidden">
    <!-- Left Side: Hero Image Section -->
    <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-end bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBBJoRJpQ9HRBVUB2KsSAmLzGbhnI_-5LPhxMYQDuw-4Czuy_T2S5m0MjqFf1mcjRg7NSp8hEZDQYv27EVLAYgqgo1kQ9_S5Q8KNq7TSBJy0Q2Xm6q9xVVWDrXvJQlUaTTYWoDggscqfSKOTBKz9qF3UQ0Gl83DGd1MlQpzdtleflmzp6T7032THp2Y_qt3IG5muuipS7E2kaGVQE1MS0Vh8aXQJfHxwlfqfQL_dB0cIoE3D1ttMhnwHRieNpvHWOyCMA5aoQUjEVY");'>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-black/40 to-transparent"></div>
        <!-- Content -->
        <div class="relative z-10 p-12 lg:p-16 flex flex-col gap-4 max-w-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-brand-yellow text-4xl">apartment</span>
                <a href="{{ route('booking.index') }}" class="text-white text-xl font-bold tracking-wide hover:underline decoration-brand-yellow decoration-2 underline-offset-4 transition-all">Housing UMY</a>
            </div>
            <h1 class="text-white text-4xl lg:text-5xl font-black leading-tight tracking-[-0.033em]">
                Selamat Datang di Housing UMY
            </h1>
            <h2 class="text-white/90 text-lg font-normal leading-relaxed max-w-md">
                Kenyamanan dan keamanan tempat tinggal selama studi Anda. Temukan hunian terbaik di lingkungan kampus.
            </h2>
            <!-- Decorative Element -->
            <div class="h-1 w-20 bg-brand-yellow rounded-full mt-4"></div>
        </div>
    </div>
    
    <!-- Right Side: Login Form -->
    <div class="flex w-full lg:w-1/2 flex-col justify-center items-center bg-white px-6 py-12 lg:px-20 overflow-y-auto relative">
        <!-- Back Button Mobile -->
        <a href="{{ route('booking.index') }}" class="absolute top-6 left-6 flex items-center gap-2 text-gray-500 hover:text-primary transition-colors lg:hidden">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            <span class="text-sm font-medium">Beranda</span>
        </a>

        <div class="w-full max-w-[480px] flex flex-col gap-6">
            <!-- Header -->
            <div class="flex flex-col gap-2 pb-2">
                <div class="flex items-center gap-2 lg:hidden mb-4 justify-center">
                    <span class="material-symbols-outlined text-primary text-3xl">apartment</span>
                    <span class="text-primary text-lg font-bold tracking-wide">Housing UMY</span>
                </div>
                <h1 class="text-[#171212] tracking-tight text-3xl font-bold leading-tight text-left">
                    Masuk ke Akun Anda
                </h1>
                <p class="text-[#856866] text-base">Silakan masukkan detail akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-green-700 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="text-red-600 text-sm list-none space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">error</span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('customer.login.submit') }}" class="flex flex-col gap-5">
                @csrf
                <!-- Email Field -->
                <label class="flex flex-col flex-1">
                    <p class="text-[#171212] text-sm font-semibold leading-normal pb-2">Email</p>
                    <div class="relative">
                        <input name="email" value="{{ old('email') }}" required autofocus
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="email@example.com" type="email"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#856866]">
                            <span class="material-symbols-outlined text-xl">person</span>
                        </div>
                    </div>
                </label>

                <!-- Password Field -->
                <label class="flex flex-col flex-1">
                    <div class="flex justify-between items-center pb-2">
                        <p class="text-[#171212] text-sm font-semibold leading-normal">Password</p>
                    </div>
                    <div class="relative">
                        <input name="password" id="password" required
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="Masukkan password" type="password"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-[#856866] hover:text-primary transition-colors" onclick="togglePassword()">
                            <span class="material-symbols-outlined text-xl" id="toggleIcon">visibility_off</span>
                        </div>
                    </div>
                </label>

                <!-- Forgot Password Link -->
                <div class="flex justify-between items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm text-[#856866]">Ingat saya</span>
                    </label>
                    <a class="text-brand-green text-sm font-medium leading-normal hover:underline transition-all" href="#">Lupa Password?</a>
                </div>

                <!-- Primary Action Button -->
                <button type="submit" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 lg:h-14 bg-primary hover:bg-[#7a1e19] text-white text-base font-bold leading-normal tracking-[0.015em] transition-colors shadow-sm hover:shadow-md">
                    <span class="truncate">Masuk</span>
                </button>
            </form>

            <!-- Divider -->
            {{-- 
            <div class="flex items-center gap-4 py-2">
                <div class="h-px flex-1 bg-[#e4dddc]"></div>
                <span class="text-[#856866] text-sm font-medium">Atau masuk dengan</span>
                <div class="h-px flex-1 bg-[#e4dddc]"></div>
            </div>

            <!-- Social Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <button type="button" class="flex items-center justify-center gap-2 h-12 rounded-lg border border-[#e4dddc] hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <svg fill="none" height="20" viewbox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23.52 12.2902C23.52 11.4591 23.4473 10.6691 23.31 9.91H12V14.52H18.47C18.18 15.99 17.34 17.25 16.08 18.1V21.06H19.93C22.19 18.99 23.52 15.93 23.52 12.2902Z" fill="#4285F4"></path>
                        <path d="M12 24C15.24 24 17.96 22.92 19.93 21.06L16.08 18.1C15 18.82 13.62 19.24 12 19.24C8.87 19.24 6.22 17.13 5.27 14.29H1.29V17.38C3.25 21.27 7.31 24 12 24Z" fill="#34A853"></path>
                        <path d="M5.27 14.29C5.03 13.43 4.9 12.52 4.9 11.6C4.9 10.68 5.03 9.77 5.27 8.91V5.82H1.29C0.47 7.45 0 9.27 0 11.6C0 13.93 0.47 15.75 1.29 17.38L5.27 14.29Z" fill="#FBBC05"></path>
                        <path d="M12 3.96C13.76 3.96 15.34 4.57 16.58 5.76L19.99 2.35C17.95 0.45 15.23 0 12 0C7.31 0 3.25 2.73 1.29 6.62L5.27 9.71C6.22 6.87 8.87 3.96 12 3.96Z" fill="#EA4335"></path>
                    </svg>
                    <span class="text-[#171212] font-semibold text-sm">Google</span>
                </button>
                <button type="button" class="flex items-center justify-center gap-2 h-12 rounded-lg border border-[#e4dddc] hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <span class="material-symbols-outlined text-[#171212] text-xl">school</span>
                    <span class="text-[#171212] font-semibold text-sm">SSO UMY</span>
                </button>
            </div>
            --}}

            <!-- Registration Link -->
            <div class="flex justify-center pt-4">
                <p class="text-[#171212] text-sm">
                    Belum punya akun? 
                    <a class="text-brand-green font-bold hover:underline ml-1" href="{{ route('customer.register') }}">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.innerText = 'visibility';
        } else {
            passwordInput.type = 'password';
            toggleIcon.innerText = 'visibility_off';
        }
    }
</script>
</body>
</html>
