import { initDashboard } from './main.js';

document.addEventListener('DOMContentLoaded', () => {
    initDashboard();

    const trackingButtons = document.querySelectorAll('[data-open-development]');
    trackingButtons.forEach((btn) => {
        btn.addEventListener('mouseover', () => {
            btn.classList.add('pulse');
        });
        btn.addEventListener('mouseout', () => {
            btn.classList.remove('pulse');
        });
    });

    const attendanceCanvas = document.getElementById('attendanceChart');
    if (attendanceCanvas && window.Chart) {
        const dataset = attendanceCanvas.dataset;
        const data = {
            labels: ['Present', 'Absent', 'On Duty', 'Leave'],
            datasets: [{
                label: 'Attendance',
                data: [
                    parseInt(dataset.present, 10) || 0,
                    parseInt(dataset.absent, 10) || 0,
                    parseInt(dataset.onDuty, 10) || 0,
                    parseInt(dataset.leave, 10) || 0
                ],
                backgroundColor: [
                    'rgba(56, 189, 248, 0.6)',
                    'rgba(248, 113, 113, 0.6)',
                    'rgba(129, 140, 248, 0.6)',
                    'rgba(253, 186, 116, 0.6)'
                ],
                borderColor: [
                    '#38bdf8',
                    '#f87171',
                    '#818cf8',
                    '#fbbf24'
                ],
                borderWidth: 2,
                hoverOffset: 12
            }]
        };

        new window.Chart(attendanceCanvas, {
            type: 'doughnut',
            data,
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#cbd5f5'
                        }
                    }
                }
            }
        });
    }

    const leaveCanvas = document.getElementById('leaveChart');
    if (leaveCanvas && window.Chart) {
        const dataset = leaveCanvas.dataset;
        new window.Chart(leaveCanvas, {
            type: 'bar',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    label: 'Leave Requests',
                    data: [
                        parseInt(dataset.approved, 10) || 0,
                        parseInt(dataset.pending, 10) || 0,
                        parseInt(dataset.rejected, 10) || 0
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.6)',
                        'rgba(250, 204, 21, 0.6)',
                        'rgba(239, 68, 68, 0.6)'
                    ],
                    borderColor: [
                        '#22c55e',
                        '#facc15',
                        '#ef4444'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#e5e7ff'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#cbd5f5' },
                        grid: { color: 'rgba(148, 163, 184, 0.2)' }
                    },
                    y: {
                        ticks: { color: '#cbd5f5', precision: 0 },
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        beginAtZero: true
                    }
                }
            }
        });
    }

});


