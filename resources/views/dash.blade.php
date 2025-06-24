<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">
    <!-- Menu Mobile Toggle -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="menuToggle" class="bg-slate-800 text-white p-2 rounded-lg shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-slate-800 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col">
        <!-- Logo -->
        <div class="p-6 border-b border-slate-700">
            <h1 class="text-3xl font-bold text-center">ERP</h1>
        </div>

        <!-- Menu Items -->
        <nav class="flex-1 py-6">
            <ul class="space-y-2 px-4">
                <li>
                    <a href="#" class="flex items-center py-3 px-4 bg-slate-700 rounded-lg text-white">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center py-3 px-4 text-slate-400 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                        <span>Sales</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center py-3 px-4 text-slate-400 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center py-3 px-4 text-slate-400 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                        <span>Orders</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center py-3 px-4 text-slate-400 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                        <span>Customers</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center py-3 px-4 text-slate-400 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Overlay para mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen">
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 pt-16 lg:pt-0">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            </div>

            <!-- KPIs Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">$2.4M</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17H7V7"></path>
                                </svg>
                                <span class="text-sm text-green-500 font-medium">+12.5%</span>
                                <span class="text-sm text-gray-500 ml-1">vs last month</span>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">1,847</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17H7V7"></path>
                                </svg>
                                <span class="text-sm text-green-500 font-medium">+8.3%</span>
                                <span class="text-sm text-gray-500 ml-1">vs last month</span>
                            </div>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Customers -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Active Customers</p>
                            <p class="text-2xl font-bold text-gray-900">892</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17H7V7"></path>
                                </svg>
                                <span class="text-sm text-green-500 font-medium">+15.2%</span>
                                <span class="text-sm text-gray-500 ml-1">vs last month</span>
                            </div>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Conversion Rate -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">3.2%</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7h10v10"></path>
                                </svg>
                                <span class="text-sm text-red-500 font-medium">-2.1%</span>
                                <span class="text-sm text-gray-500 ml-1">vs last month</span>
                            </div>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Sales Overview -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Sales Overview</h2>
                    <div id="salesChart" class="w-full h-64"></div>
                </div>

                <!-- Revenue by Category -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Revenue by Category</h2>
                    <div id="revenueChart" class="w-full h-64"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Products</h2>
                    <div id="productsChart" class="w-full h-64"></div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-2 font-medium text-gray-600">Order ID</th>
                                    <th class="text-left py-3 px-2 font-medium text-gray-600">Customer</th>
                                    <th class="text-left py-3 px-2 font-medium text-gray-600">Date</th>
                                    <th class="text-left py-3 px-2 font-medium text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-2 text-gray-900">1001</td>
                                    <td class="py-3 px-2 text-gray-900">John Doe</td>
                                    <td class="py-3 px-2 text-gray-900">06/24/25</td>
                                    <td class="py-3 px-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Shipped</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-2 text-gray-900">1002</td>
                                    <td class="py-3 px-2 text-gray-900">Jane Smith</td>
                                    <td class="py-3 px-2 text-gray-900">06/24/25</td>
                                    <td class="py-3 px-2">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Pending</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-2 text-gray-900">1003</td>
                                    <td class="py-3 px-2 text-gray-900">John Doe</td>
                                    <td class="py-3 px-2 text-gray-900">05/20/25</td>
                                    <td class="py-3 px-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Shipped</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-2 text-gray-900">1004</td>
                                    <td class="py-3 px-2 text-gray-900">Dan Schulte</td>
                                    <td class="py-3 px-2 text-gray-900">06/20/25</td>
                                    <td class="py-3 px-2">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Pending</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menu Toggle Functionality
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Close menu on window resize if screen becomes large
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });

        // Sales Overview Chart
        const salesChart = echarts.init(document.getElementById('salesChart'));
        const salesOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross'
                }
            },
            grid: {
                left: '50px',
                right: '30px',
                top: '30px',
                bottom: '50px'
            },
            xAxis: {
                type: 'category',
                data: ['Jan', 'Feb', 'Mar', 'Apr', 'Jun'],
                axisLine: {
                    lineStyle: {
                        color: '#e2e8f0'
                    }
                },
                axisLabel: {
                    color: '#64748b'
                }
            },
            yAxis: {
                type: 'value',
                min: 0,
                max: 100,
                interval: 20,
                axisLabel: {
                    formatter: '${value}k',
                    color: '#64748b'
                },
                axisLine: {
                    show: false
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9'
                    }
                }
            },
            series: [{
                    name: 'Sales',
                    data: [55, 62, 58, 80, 70, 100, 80],
                    type: 'line',
                    smooth: true,
                    lineStyle: {
                        color: '#3b82f6',
                        width: 3
                    },
                    itemStyle: {
                        color: '#3b82f6'
                    },
                    symbol: 'circle',
                    symbolSize: 6
                },
                {
                    name: 'Revenue',
                    data: [42, 48, 45, 55, 78, 70, 80],
                    type: 'line',
                    smooth: true,
                    lineStyle: {
                        color: '#06b6d4',
                        width: 3
                    },
                    itemStyle: {
                        color: '#06b6d4'
                    },
                    symbol: 'circle',
                    symbolSize: 6
                }
            ]
        };
        salesChart.setOption(salesOption);

        // Revenue by Category Chart
        const revenueChart = echarts.init(document.getElementById('revenueChart'));
        const revenueOption = {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            series: [{
                name: 'Revenue',
                type: 'pie',
                radius: ['30%', '70%'],
                center: ['50%', '50%'],
                data: [{
                        value: 40,
                        name: 'Category A',
                        itemStyle: {
                            color: '#3b82f6'
                        }
                    },
                    {
                        value: 40,
                        name: 'Category B',
                        itemStyle: {
                            color: '#06b6d4'
                        }
                    },
                    {
                        value: 20,
                        name: 'Category C',
                        itemStyle: {
                            color: '#10b981'
                        }
                    }
                ],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                label: {
                    show: true,
                    position: 'inside',
                    formatter: '{d}%',
                    fontSize: 14,
                    fontWeight: 'bold',
                    color: '#fff'
                }
            }]
        };
        revenueChart.setOption(revenueOption);

        // Top Products Chart
        const productsChart = echarts.init(document.getElementById('productsChart'));
        const productsOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            grid: {
                left: '50px',
                right: '30px',
                top: '30px',
                bottom: '50px'
            },
            xAxis: {
                type: 'category',
                data: ['Product A', 'Product B', 'Product C', 'Product D', 'Other'],
                axisLine: {
                    lineStyle: {
                        color: '#e2e8f0'
                    }
                },
                axisLabel: {
                    color: '#64748b',
                    rotate: 45
                }
            },
            yAxis: {
                type: 'value',
                max: 8,
                interval: 2,
                axisLabel: {
                    formatter: '{value}k',
                    color: '#64748b'
                },
                axisLine: {
                    show: false
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9'
                    }
                }
            },
            series: [{
                data: [3.5, 5, 3.8, 6, 7],
                type: 'bar',
                itemStyle: {
                    color: '#3b82f6',
                    borderRadius: [4, 4, 0, 0]
                },
                barWidth: '60%'
            }]
        };
        productsChart.setOption(productsOption);

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            salesChart.resize();
            revenueChart.resize();
            productsChart.resize();
        });
    </script>
</body>

</html>