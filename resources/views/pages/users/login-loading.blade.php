<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging In...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        .pulse-icon {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .slide-up {
            animation: slideUp 0.8s ease-out;
        }

        .dot-1 { animation: bounce 1.4s infinite; animation-delay: 0s; }
        .dot-2 { animation: bounce 1.4s infinite; animation-delay: 0.2s; }
        .dot-3 { animation: bounce 1.4s infinite; animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 80%, 100% { 
                transform: translateY(0); 
            }
            40% { 
                transform: translateY(-12px); 
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0D1517 0%, #163F44 50%, #0D1517 100%); font-family: system-ui, -apple-system, sans-serif; overflow: hidden;" class="bg-gradient-to-br from-[#0D1517] via-[#163F44] to-[#0D1517] min-h-screen flex items-center justify-center overflow-hidden">
    <div style="text-align: center; animation: fadeIn 0.6s ease-out;" class="fade-in text-center relative">
        <!-- Decorative circles -->
        <div style="position: absolute; top: -80px; left: -80px; width: 160px; height: 160px; background: rgba(42, 163, 239, 0.1); border-radius: 50%; filter: blur(60px);" class="absolute -top-20 -left-20 w-40 h-40 bg-[#2aa3ef]/10 rounded-full blur-3xl"></div>
        <div style="position: absolute; bottom: -80px; right: -80px; width: 160px; height: 160px; background: rgba(42, 163, 239, 0.1); border-radius: 50%; filter: blur(60px);" class="absolute -bottom-20 -right-20 w-40 h-40 bg-[#2aa3ef]/10 rounded-full blur-3xl"></div>

        <!-- Main content -->
        <div style="position: relative; z-index: 10;" class="relative z-10">
            <!-- Logo/Icon with spinner -->
            <div style="position: relative; display: inline-block; margin-bottom: 32px;" class="relative inline-block mb-8">
                <div style="width: 96px; height: 96px; border: 4px solid #2aa3ef; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;" class="spinner w-24 h-24 border-4 border-t-transparent border-[#2aa3ef] rounded-full"></div>
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; animation: pulse 1.5s ease-in-out infinite;" class="absolute inset-0 flex items-center justify-center pulse-icon">
                    <svg style="width: 48px; height: 48px; color: #2aa3ef;" class="w-12 h-12 text-[#2aa3ef]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Text -->
            <div style="animation: slideUp 0.8s ease-out;" class="slide-up">
                <h2 style="font-size: 30px; font-weight: bold; color: white; margin-bottom: 12px;" class="text-3xl font-bold text-white mb-3">Welcome Back!</h2>
                <p style="color: #d1d5db; font-size: 18px; margin-bottom: 8px;" class="text-gray-300 text-lg mb-2">Logging you in</p>
                
                <!-- Animated dots -->
                <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 32px;" class="flex justify-center gap-2 mb-8">
                    <div style="width: 12px; height: 12px; background: #2aa3ef; border-radius: 50%;" class="dot-1 w-3 h-3 bg-[#2aa3ef] rounded-full"></div>
                    <div style="width: 12px; height: 12px; background: #2aa3ef; border-radius: 50%;" class="dot-2 w-3 h-3 bg-[#2aa3ef] rounded-full"></div>
                    <div style="width: 12px; height: 12px; background: #2aa3ef; border-radius: 50%;" class="dot-3 w-3 h-3 bg-[#2aa3ef] rounded-full"></div>
                </div>

                <!-- Progress bar -->
                <div style="width: 256px; height: 4px; background: #374151; border-radius: 9999px; overflow: hidden; margin: 0 auto;" class="w-64 h-1 bg-gray-700 rounded-full overflow-hidden mx-auto">
                    <div style="height: 100%; width: 70%; background: linear-gradient(90deg, #2aa3ef 0%, #27D5E8 100%); border-radius: 9999px; animation: pulse 1.5s ease-in-out infinite; transition: width 1.5s ease-in-out;" class="h-full bg-gradient-to-r from-[#2aa3ef] to-[#27D5E8] rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect ke home setelah 1.5 detik
        setTimeout(function() {
            window.location.href = "{{ route('posts.index') }}";
        }, 1500);
    </script>
</body>
</html>
