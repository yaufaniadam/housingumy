<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Housing UMY Register</title>
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
                Bergabunglah dengan Housing UMY
            </h1>
            <h2 class="text-white/90 text-lg font-normal leading-relaxed max-w-md">
                Daftar sekarang untuk mendapatkan akses mudah ke layanan pemesanan kamar dan fasilitas kampus.
            </h2>
            <!-- Decorative Element -->
            <div class="h-1 w-20 bg-brand-yellow rounded-full mt-4"></div>
        </div>
    </div>
    
    <!-- Right Side: Register Form -->
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
                    Buat Akun Baru
                </h1>
                <p class="text-[#856866] text-base">Lengkapi data diri Anda untuk mendaftar.</p>
            </div>

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
            <form method="POST" action="{{ route('customer.register.submit') }}" class="flex flex-col gap-5">
                @csrf
                
                <!-- Name Field -->
                <label class="flex flex-col flex-1">
                    <p class="text-[#171212] text-sm font-semibold leading-normal pb-2">Nama Lengkap</p>
                    <div class="relative">
                        <input name="name" value="{{ old('name') }}" required autofocus
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="Nama Lengkap Anda" type="text"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#856866]">
                            <span class="material-symbols-outlined text-xl">badge</span>
                        </div>
                    </div>
                </label>

                <!-- Email Field -->
                <label class="flex flex-col flex-1">
                    <p class="text-[#171212] text-sm font-semibold leading-normal pb-2">Email</p>
                    <div class="relative">
                        <input name="email" value="{{ old('email') }}" required
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="email@example.com" type="email"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#856866]">
                            <span class="material-symbols-outlined text-xl">email</span>
                        </div>
                    </div>
                </label>

                <!-- Phone Field -->
                <label class="flex flex-col flex-1">
                    <p class="text-[#171212] text-sm font-semibold leading-normal pb-2">No. Telepon</p>
                    <div class="relative">
                        <input name="phone" value="{{ old('phone') }}" required
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="08xxxxxxxxxx" type="tel"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#856866]">
                            <span class="material-symbols-outlined text-xl">phone</span>
                        </div>
                    </div>
                </label>

                <!-- Password Field -->
                <label class="flex flex-col flex-1">
                    <p class="text-[#171212] text-sm font-semibold leading-normal pb-2">Password</p>
                    <div class="relative">
                        <input name="password" id="password" required
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="Minimal 8 karakter" type="password"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-[#856866] hover:text-primary transition-colors" onclick="togglePassword('password', 'toggleIcon')">
                            <span class="material-symbols-outlined text-xl" id="toggleIcon">visibility_off</span>
                        </div>
                    </div>
                </label>

                <!-- Password Confirmation Field -->
                <label class="flex flex-col flex-1">
                    <div class="flex justify-between items-center pb-2">
                        <p class="text-[#171212] text-sm font-semibold leading-normal">Konfirmasi Password</p>
                    </div>
                    <div class="relative">
                        <input name="password_confirmation" id="password_confirmation" required
                               class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#171212] focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#e4dddc] bg-white focus:border-primary h-12 lg:h-14 placeholder:text-[#856866] px-[15px] text-base font-normal leading-normal transition-colors" 
                               placeholder="Ulangi password" type="password"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-[#856866] hover:text-primary transition-colors" onclick="togglePassword('password_confirmation', 'toggleIconConfirm')">
                            <span class="material-symbols-outlined text-xl" id="toggleIconConfirm">visibility_off</span>
                        </div>
                    </div>
                </label>

                <!-- Primary Action Button -->
                <button type="submit" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 lg:h-14 bg-primary hover:bg-[#7a1e19] text-white text-base font-bold leading-normal tracking-[0.015em] transition-colors shadow-sm hover:shadow-md mt-2">
                    <span class="truncate">Daftar</span>
                </button>
            </form>

            <!-- Login Link -->
            <div class="flex justify-center pt-4">
                <p class="text-[#171212] text-sm">
                    Sudah punya akun? 
                    <a class="text-brand-green font-bold hover:underline ml-1" href="{{ route('customer.login') }}">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
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
