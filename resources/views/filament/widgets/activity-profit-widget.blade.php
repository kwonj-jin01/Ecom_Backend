<x-filament-widgets::widget>
    <div class="profit-widget-container opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
        <div class="flex flex-col items-center space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rentabilité de l'activité</h3>

            <!-- Donut Chart -->
            <div class="relative w-32 h-32">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <!-- Background circle -->
                    <path d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none" stroke="#E5E7EB" stroke-width="3" />

                    <!-- Foreground arc -->
                    <path d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none" stroke="#EF4444"
                        stroke-width="3"
                        stroke-dasharray="0, 100"
                        stroke-linecap="round"
                        class="profit-chart-arc transition-all duration-1000 ease-out"
                        data-target="{{ $rentabilite }}"
                        data-profit="{{ $profit }}" />
                </svg>

                <!-- Pourcentage -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-red-600 profit-percentage-counter"
                          data-target="{{ $rentabilite }}"
                          data-duration="1000"
                          data-profit="{{ $profit }}"
                          wire:ignore>0%</span>
                </div>

                <!-- Loading spinner -->
                <div class="profit-loading-spinner absolute inset-0 flex items-center justify-center hidden">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                </div>
            </div>

            <!-- Détails -->
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1 mt-2">
                <div>
                    <strong>Revenu :</strong>
                    <span class="profit-amount-counter"
                          data-target="{{ $revenu }}"
                          data-duration="1500"
                          wire:ignore>0</span> F CFA
                </div>
                <div>
                    <strong>Dépenses :</strong>
                    <span class="expense-amount-counter"
                          data-target="{{ $depenses }}"
                          data-duration="1500"
                          wire:ignore>0</span> F CFA
                </div>
                <div>
                    <strong>Bénéfice :</strong>
                    <span class="benefit-amount-counter"
                          data-target="{{ $profit }}"
                          data-duration="1500"
                          data-profit="{{ $profit }}"
                          wire:ignore>0</span> F CFA
                </div>
            </div>
        </div>
    </div>

    <style>
        .profit-widget-container.widget-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .profit-percentage-counter,
        .profit-amount-counter,
        .expense-amount-counter,
        .benefit-amount-counter {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 600;
        }

        .profit-chart-arc {
            transition: stroke-dasharray 1000ms ease-out, stroke 500ms ease-out;
        }

        /* Couleurs selon la rentabilité */
        .profit-negative {
            color: #EF4444 !important; /* rouge */
        }

        .profit-low {
            color: #F59E0B !important; /* jaune */
        }

        .profit-good {
            color: #10B981 !important; /* vert */
        }

        .spinner-negative {
            border-color: #EF4444 !important;
        }

        .spinner-low {
            border-color: #F59E0B !important;
        }

        .spinner-good {
            border-color: #10B981 !important;
        }

        @keyframes profit-pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .profit-chart-arc.animating {
            animation: profit-pulse 2s ease-in-out infinite;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profitObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const widgetContainer = entry.target;
                        setTimeout(() => {
                            widgetContainer.classList.add('widget-visible');
                            startProfitAnimations(widgetContainer);
                        }, 300);
                        profitObserver.unobserve(widgetContainer);
                    }
                });
            }, {
                threshold: 0.1
            });

            const profitWidget = document.querySelector('.profit-widget-container');
            if (profitWidget) {
                profitObserver.observe(profitWidget);
            }
        });

        function startProfitAnimations(container) {
            const loadingSpinner = container.querySelector('.profit-loading-spinner');
            const chartArc = container.querySelector('.profit-chart-arc');
            const percentageCounter = container.querySelector('.profit-percentage-counter');
            const benefitCounter = container.querySelector('.benefit-amount-counter');

            // Déterminer les couleurs selon la rentabilité
            const profit = parseFloat(chartArc.getAttribute('data-profit'));
            const rentability = parseFloat(chartArc.getAttribute('data-target'));

            let colorClass, strokeColor, spinnerClass;

            if (profit < 0) {
                colorClass = 'profit-negative';
                strokeColor = '#EF4444'; // rouge
                spinnerClass = 'spinner-negative';
            } else if (rentability >= 0 && rentability < 50) {
                colorClass = 'profit-low';
                strokeColor = '#F59E0B'; // jaune
                spinnerClass = 'spinner-low';
            } else {
                colorClass = 'profit-good';
                strokeColor = '#10B981'; // vert
                spinnerClass = 'spinner-good';
            }

            // Appliquer les couleurs
            if (percentageCounter) {
                percentageCounter.classList.add(colorClass);
            }
            if (benefitCounter) {
                benefitCounter.classList.add(colorClass);
            }
            if (chartArc) {
                chartArc.style.stroke = strokeColor;
            }
            if (loadingSpinner) {
                loadingSpinner.querySelector('div').classList.add(spinnerClass);
            }

            // Afficher le spinner
            if (loadingSpinner) {
                loadingSpinner.style.display = 'flex';
            }

            // Animer le graphique en donut
            if (chartArc) {
                const targetPercentage = Math.abs(rentability); // Valeur absolue pour l'affichage
                setTimeout(() => {
                    chartArc.style.strokeDasharray = `${targetPercentage}, 100`;
                    chartArc.classList.add('animating');
                }, 500);
            }

            // Animer les compteurs
            const counters = container.querySelectorAll('.profit-percentage-counter, .profit-amount-counter, .expense-amount-counter, .benefit-amount-counter');

            counters.forEach((counter, index) => {
                setTimeout(() => {
                    animateProfitCounter(counter);
                }, index * 200 + 700);
            });

            // Masquer le spinner après les animations
            setTimeout(() => {
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
                if (chartArc) {
                    chartArc.classList.remove('animating');
                }
            }, 2500);
        }

        function animateProfitCounter(counterElement) {
            const targetValue = parseFloat(counterElement.getAttribute('data-target'));
            const animationDuration = parseInt(counterElement.getAttribute('data-duration')) || 1500;
            const isPercentage = counterElement.classList.contains('profit-percentage-counter');

            const animationStart = Date.now();
            const initialValue = 0;

            function updateProfitDisplay() {
                const timeElapsed = Date.now() - animationStart;
                const animationProgress = Math.min(timeElapsed / animationDuration, 1);
                const easeInOutSine = progress => -(Math.cos(Math.PI * progress) - 1) / 2;
                const smoothProgress = easeInOutSine(animationProgress);
                const currentNumber = initialValue + (targetValue - initialValue) * smoothProgress;

                if (isPercentage) {
                    counterElement.textContent = `${Math.round(currentNumber * 100) / 100}%`;
                } else {
                    // Formatage avec signe pour les montants négatifs
                    const formattedNumber = Math.round(currentNumber).toLocaleString('fr-FR');
                    counterElement.textContent = currentNumber < 0 ? `-${Math.abs(Math.round(currentNumber)).toLocaleString('fr-FR')}` : formattedNumber;
                }

                if (animationProgress < 1) {
                    requestAnimationFrame(updateProfitDisplay);
                } else {
                    if (isPercentage) {
                        counterElement.textContent = `${targetValue}%`;
                    } else {
                        const finalFormatted = Math.round(targetValue).toLocaleString('fr-FR');
                        counterElement.textContent = targetValue < 0 ? `-${Math.abs(Math.round(targetValue)).toLocaleString('fr-FR')}` : finalFormatted;
                    }
                }
            }

            updateProfitDisplay();
        }
    </script>
</x-filament-widgets::widget>
