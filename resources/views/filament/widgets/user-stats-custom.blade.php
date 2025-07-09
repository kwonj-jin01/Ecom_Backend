<x-filament-widgets::widget>
    <div class="flex flex-col gap-8">
        @foreach ($this->getStats() as $index => $stat)
            <div class="bg-white rounded-xl shadow p-4 {{ $stat['color'] }} text-black">
                <div class="text-sm font-medium">{{ $stat['title'] }}</div>
                <div class="text-2xl font-bold animated-counter"
                    data-target="{{ preg_replace('/[^0-9]/', '', $stat['value']) }}" data-index="{{ $index }}"
                    data-duration="2000" wire:ignore>
                    {{ $stat['value'] }}
                </div>
            </div>
        @endforeach
    </div>
    <style>
        .animated-counter {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-right: 8px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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
        .dark .animated-counter {
            color: #f9fafb;
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
