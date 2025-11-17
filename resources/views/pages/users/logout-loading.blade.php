<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes wave {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        .slide-down {
            animation: slideDown 0.7s ease-out;
        }

        .wave-1 { animation: wave 1.2s infinite; animation-delay: 0s; }
        .wave-2 { animation: wave 1.2s infinite; animation-delay: 0.15s; }
        .wave-3 { animation: wave 1.2s infinite; animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#0D1517] via-[#1a4d55] to-[#0D1517] min-h-screen flex items-center justify-center overflow-hidden">
    <div class="fade-in text-center relative">
        <!-- Decorative elements -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>

        <!-- Main content -->
        <div class="relative z-10">
            <!-- Spinner with logout icon -->
            <div class="relative inline-block mb-8">
                <div class="spinner w-24 h-24 border-4 border-t-transparent border-emerald-400 rounded-full"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-12 h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>

            <!-- Text content -->
            <div class="slide-down">
                <h2 class="text-3xl font-bold text-white mb-3">Goodbye!</h2>
                <p class="text-gray-300 text-lg mb-6">Logging you out safely</p>
                
                <!-- Animated waves -->
                <div class="flex justify-center gap-2 mb-8">
                    <div class="wave-1 w-3 h-3 bg-emerald-400 rounded-full"></div>
                    <div class="wave-2 w-3 h-3 bg-emerald-400 rounded-full"></div>
                    <div class="wave-3 w-3 h-3 bg-emerald-400 rounded-full"></div>
                </div>

                <!-- Success message -->
                <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl p-4 backdrop-blur-sm max-w-sm mx-auto">
                    <p class="text-emerald-300 font-medium">See you next time! 👋</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect ke login setelah 2 detik
        setTimeout(function() {
            window.location.href = "/";
        }, 2000);
    </script>
</body>
</html>
