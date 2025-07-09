<x-filament-widgets::widget>
    <div class="flex flex-col gap-8">
        @foreach ($this->getStats() as $index => $stat)
            <div class="bg-white rounded-xl shadow p-4 {{ $stat['color'] }} text-black stats-widget-card opacity-0 transform translate-y-4 transition-all duration-500 ease-out"
                data-index="{{ $index }}">
                <div class="text-sm font-medium mb-2">{{ $stat['title'] }}</div>
                <div class="relative">
                    <div class="text-2xl font-bold stats-number-counter"
                        data-target="{{ preg_replace('/[^0-9]/', '', $stat['value']) }}"
                        data-index="{{ $index }}"
                        data-duration="2000"
                        wire:ignore>
                        0
                    </div>
                    <div class="widget-loading-spinner absolute inset-0 flex items-center justify-center hidden">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .stats-number-counter {
            font-size: 2.5rem;
            font-weight: 700;
            color: inherit;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .stats-widget-card.card-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes number-pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        /* Mode sombre */
        .dark .stats-number-counter {
            color: #f9fafb;
        }

        .dark .bg-white {
            background-color: #374151;
        }

        .dark .text-white {
            color: #f9fafb;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewportObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const widgetCard = entry.target;
                        const cardIndex = parseInt(widgetCard.getAttribute('data-index'));
                        setTimeout(() => {
                            widgetCard.classList.add('card-visible');
                            startNumberAnimation(widgetCard);
                        }, cardIndex * 200);
                        viewportObserver.unobserve(widgetCard);
                    }
                });
            }, {
                threshold: 0.1
            });

            const widgetCards = document.querySelectorAll('.stats-widget-card');
            widgetCards.forEach(card => viewportObserver.observe(card));
        });

        function startNumberAnimation(widgetCard) {
            const numberCounter = widgetCard.querySelector('.stats-number-counter');
            const loadingSpinner = widgetCard.querySelector('.widget-loading-spinner');
            if (!numberCounter) return;

            if (loadingSpinner) {
                loadingSpinner.style.display = 'flex';
            }

            const targetValue = parseInt(numberCounter.getAttribute('data-target'));
            const animationDuration = parseInt(numberCounter.getAttribute('data-duration')) || 2000;

            runCounterAnimation(numberCounter, targetValue, animationDuration, () => {
                if (loadingSpinner) {
                    setTimeout(() => loadingSpinner.style.display = 'none', 300);
                }
            });
        }

        function runCounterAnimation(counterElement, finalValue, totalDuration, onFinish = null) {
            const animationStart = Date.now();
            const initialValue = 0;

            function updateNumberDisplay() {
                const timeElapsed = Date.now() - animationStart;
                const animationProgress = Math.min(timeElapsed / totalDuration, 1);
                const easeInOutSine = progress => -(Math.cos(Math.PI * progress) - 1) / 2;
                const smoothProgress = easeInOutSine(animationProgress);
                const currentNumber = Math.ceil(initialValue + (finalValue - initialValue) * smoothProgress);

                // Format number with spaces as thousands separator (French format)
                counterElement.textContent = currentNumber.toLocaleString('fr-FR');

                if (animationProgress < 1) {
                    requestAnimationFrame(updateNumberDisplay);
                } else {
                    counterElement.textContent = finalValue.toLocaleString('fr-FR');
                    if (onFinish) onFinish();
                }
            }

            updateNumberDisplay();
        }
    </script>
</x-filament-widgets::widget>
