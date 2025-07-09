<div class="animated-counter" data-target="{{ $value }}" data-index="{{ $index }}" data-duration="2000"
    wire:ignore>
    0
</div>

<div class="stats-loading-indicator" style="display: none;">
    <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
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
