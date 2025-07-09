<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): string => '<style>

                /* Sidebar width */
                .fi-sidebar {
                    width: 12rem !important;
                    background-color: #041643;
                    }

                /*  card */
                .fi-wi-stats-overview-stat{
                    background: white;
                    border-radius: 0.75rem;
                    padding: 1.5rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e2e8f0;
                    transition: all 0.2s;
                    }
                .bg-success {
                    background-color: #f0fdf4; /* green-50 */
                }

                .bg-danger {
                    background-color: #fef2f2; /* red-50 */
                }

                .bg-warning {
                    background-color: #fffbeb; /* yellow-50 */
                }

                .bg-info {
                    background-color: #f0f9ff; /* sky-50 */
                }

                .bg-primary {
                    background-color: #eff6ff; /* blue-50 */
                }


                .fi-wi-stats-overview-stat:hover{
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    transform: translateY(-2px);
                    }

                /*  card element */
                .fi-wi-stats-overview-stat-value{
                    font-size: 0.875rem;
                    color: #64748b;
                    font-weight: 500;
                    }

                /* Chart Container */
                .chart-container {
                    display: grid;
                    grid-template-columns: 2fr 1fr 1fr;
                    gap: 1.5rem;
                    margin-bottom: 1rem;
                    }
                .chart-card {
                    border-radius: 0.75rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e2e8f0;
                    height: 360px;
                    width: 664px;
                    }

                /* Sidebar Cards */
                .sidebar-cards {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }
                .sidebar-cards-right {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }


                /* Modification des breakpoints Tailwind */
                @media (min-width: 640px) {
                    .sm\:grid-cols-2 {
                        grid-template-columns: repeat(2, minmax(0px, 1fr));
                    }
                }

                @media (min-width: 768px) {
                    .md\:grid-cols-3 {
                        grid-template-columns: repeat(3, minmax(0px, 1fr));
                    }

                    /* Personnalisation pour tes cartes */
                    .md\:grid-cols-5 {
                        grid-template-columns: repeat(5, minmax(0px, 1fr));
                    }
                }

                /* Override des classes Filament existantes */
                @media (min-width: 768px) {
                    .fi-wi-stats-overview-stats-ctn {
                        grid-template-columns: repeat(5, minmax(0px, 1fr)) !important;
                    }
                }

                /* Breakpoints personnalisés */
                @media (min-width: 900px) {
                    .custom\:grid-cols-5 {
                        grid-template-columns: repeat(5, minmax(0px, 1fr));
                    }
                }

                @media (min-width: 1024px) {
                    .lg\:grid-cols-4 {
                        grid-template-columns: repeat(4, minmax(0px, 1fr));
                    }

                    .lg\:grid-cols-6 {
                        grid-template-columns: repeat(6, minmax(0px, 1fr));
                    }
                }
            </style>'


        );
    }
}
