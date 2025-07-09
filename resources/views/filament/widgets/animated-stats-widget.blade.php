<x-filament-widgets::widget>


    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5  p-5 h-250 w-250">
        @foreach ($this->getStats() as $index => $stat)
            <div class="animated-stats-card" data-index="{{ $index }}">
                <div class="stats-card-inner" style="border-left: 4px solid {{ $stat['color'] }};">

                    {{-- Valeur principale animée --}}
                    <div class="stats-value-container">
                        <div class="stats-main-value">
                            <span class="animated-counter" data-target="{{ $stat['value'] }}" data-duration="4000">
                                0
                            </span>
                            <span class="stats-currency">{{ $stat['currency'] }}</span>
                        </div>
                    </div>

                    {{-- Indicateur d'animation --}}
                    <div class="stats-loading-indicator" style="display: none;">
                        <div class="loading-dot" style="background-color: {{ $stat['color'] }};"></div>
                        <span class="loading-text">Mise à jour...</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .animated-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .animated-stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .animated-stats-card.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .animated-stats-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .stats-card-inner {
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stats-value-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-main-value {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .animated-counter {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-right: 8px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .stats-currency {
            font-size: 1.5rem;
            font-weight: 600;
            color: #6b7280;
        }

        .stats-subtitle {
            font-size: 0.875rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .stats-label {
            font-size: 1.125rem;
            font-weight: 600;
            color: #374151;
            text-align: center;
            margin-bottom: 12px;
        }

        .stats-description {
            font-size: 0.975rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 16px;
        }

        .stats-chart {
            margin-bottom: 16px;
            height: 30px;
        }

        .sparkline {
            width: 100%;
            height: 30px;
            display: block;
        }

        .stats-decorator-line {
            height: 3px;
            border-radius: 2px;
            margin: 16px 0;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .stats-loading-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
            padding-top: 12px;
        }

        .loading-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .loading-text {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Mode sombre */
        .dark .animated-stats-card {
            background: #1f2937;
        }

        .dark .animated-counter {
            color: #f9fafb;
        }

        .dark .stats-label {
            color: #e5e7eb;
        }

        .dark .stats-subtitle {
            color: #9ca3af;
        }

        .dark .loading-text {
            color: #9ca3af;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const card = entry.target;
                        const index = parseInt(card.getAttribute('data-index'));
                        setTimeout(() => {
                            card.classList.add('animate-in');
                            animateCardCounters(card);
                        }, index * 200);
                        observer.unobserve(card);
                    }
                });
            }, {
                threshold: 0.1
            });

            const cards = document.querySelectorAll('.animated-stats-card');
            cards.forEach(card => observer.observe(card));
        });

        function animateCardCounters(card) {
            const counter = card.querySelector('.animated-counter');
            const loadingIndicator = card.querySelector('.stats-loading-indicator');
            if (!counter) return;
            loadingIndicator.style.display = 'flex';
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = parseInt(counter.getAttribute('data-duration')) || 4000;
            animateCounter(counter, target, duration, () => {
                setTimeout(() => loadingIndicator.style.display = 'none', 1000);
            });
        }

        function animateCounter(element, target, duration, onComplete = null) {
            const startTime = Date.now();
            const startValue = 0;

            function updateCounter() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeInOutSine = progress => -(Math.cos(Math.PI * progress) - 1) / 2;
                const easedProgress = easeInOutSine(progress);
                const currentValue = Math.ceil(startValue + (target - startValue) * easedProgress);
                element.textContent = currentValue.toLocaleString('fr-FR');

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target.toLocaleString('fr-FR');
                    if (onComplete) onComplete();
                }
            }

            updateCounter();
        }
    </script>
</x-filament-widgets::widget>
