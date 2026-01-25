<!-- Achievement Popup Component -->
<div x-show="showAchievement" 
     x-transition.opacity.duration.500ms
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
  
  <div x-show="showAchievement"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-90"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-90"
       class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 text-center relative overflow-hidden">
    
    <!-- Confetti Animation Background -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="confetti-container"></div>
    </div>
    
    <!-- Close Button -->
    <button @click="showAchievement = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
    
    <!-- Achievement Icon -->
    <div class="mb-6">
      <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg animate-bounce">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
      </div>
    </div>
    
    <!-- Achievement Content -->
    <div class="mb-6">
      <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="currentAchievement.title"></h3>
      <p class="text-gray-600 text-lg" x-text="currentAchievement.description"></p>
    </div>
    
    <!-- Points Earned -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-4 mb-6">
      <div class="flex items-center justify-center gap-2">
        <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
        </svg>
        <span class="text-lg font-semibold text-gray-700">+<span x-text="currentAchievement.points"></span> Poin</span>
      </div>
    </div>
    
    <!-- Share Button -->
    <div class="flex gap-3">
      <button @click="shareAchievement()"
              class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
        Bagikan Pencapaian
      </button>
      <button @click="showAchievement = false"
              class="px-6 py-3 rounded-xl font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        Tutup
      </button>
    </div>
  </div>
</div>

<!-- Confetti CSS -->
<style>
.confetti-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  pointer-events: none;
}

.confetti {
  position: absolute;
  width: 10px;
  height: 10px;
  background: #f59e0b;
  animation: confetti-fall 3s linear infinite;
}

.confetti:nth-child(1) { left: 10%; animation-delay: 0s; background: #ef4444; }
.confetti:nth-child(2) { left: 20%; animation-delay: 0.2s; background: #3b82f6; }
.confetti:nth-child(3) { left: 30%; animation-delay: 0.4s; background: #10b981; }
.confetti:nth-child(4) { left: 40%; animation-delay: 0.6s; background: #f59e0b; }
.confetti:nth-child(5) { left: 50%; animation-delay: 0.8s; background: #8b5cf6; }
.confetti:nth-child(6) { left: 60%; animation-delay: 1s; background: #ef4444; }
.confetti:nth-child(7) { left: 70%; animation-delay: 1.2s; background: #3b82f6; }
.confetti:nth-child(8) { left: 80%; animation-delay: 1.4s; background: #10b981; }
.confetti:nth-child(9) { left: 90%; animation-delay: 1.6s; background: #f59e0b; }

@keyframes confetti-fall {
  0% {
    transform: translateY(-100px) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(400px) rotate(720deg);
    opacity: 0;
  }
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0, -30px, 0);
  }
  70% {
    transform: translate3d(0, -15px, 0);
  }
  90% {
    transform: translate3d(0, -4px, 0);
  }
}
</style>
