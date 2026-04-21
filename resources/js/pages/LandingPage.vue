<template>
    <DemoModal :isOpen="showDemo" @close="showDemo = false" />
    <AuthModal />
    <div
        class="min-h-screen bg-[#0D0D12] text-white overflow-x-hidden font-sans"
    >
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 bg-[#0D0D12]/90 backdrop-blur-md">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">
                    <div class="flex items-center gap-2.5">
                        <div class="relative">
                            <div
                                class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 flex items-center justify-center"
                            >
                                <div
                                    class="w-2 h-2 rounded-full bg-white animate-ping absolute"
                                ></div>
                                <div
                                    class="w-2 h-2 rounded-full bg-white relative"
                                ></div>
                            </div>
                        </div>
                        <span
                            class="text-lg font-bold tracking-wide"
                            :style="{ fontFamily: 'Space Mono, monospace' }"
                            >PulseCheck</span
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            @click="authModal.open('signin')"
                            class="text-sm text-gray-500 hover:text-white transition-colors"
                        >
                            Sign In
                        </button>
                        <button
                            @click="handleDemo"
                            class="px-3.5 py-1.5 bg-gradient-to-r from-cyan-600 to-teal-600 text-sm font-medium rounded-md hover:from-cyan-500 hover:to-teal-500 transition-all shadow-lg shadow-cyan-500/25"
                        >
                            Demo
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section
            class="relative pt-24 pb-16 px-4 sm:px-6 lg:px-8 overflow-hidden"
        >
            <!-- Background Effects -->
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute top-20 left-0 w-[600px] h-[600px] bg-cyan-500/5 rounded-full blur-[140px]"
                ></div>
                <div
                    class="absolute top-40 right-0 w-[500px] h-[500px] bg-teal-500/5 rounded-full blur-[120px]"
                ></div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-emerald-500/5 rounded-full blur-[100px]"
                ></div>
            </div>

            <div class="max-w-6xl mx-auto relative">
                <div class="text-center space-y-6 mb-16">
                    <!-- Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r from-cyan-500/10 via-teal-500/10 to-emerald-500/10 border border-cyan-500/20 text-sm"
                    >
                        <div class="relative flex items-center justify-center">
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping absolute"
                            ></div>
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-cyan-400"
                            ></div>
                        </div>
                        <span
                            class="text-gray-300"
                            :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                            >self-hosted • open-source</span
                        >
                    </div>

                    <!-- Headline -->
                    <h1
                        class="text-5xl sm:text-6xl lg:text-7xl font-black leading-[1.1] tracking-tight"
                        :style="{
                            fontFamily:
                                'Cabinet Grotesk, system-ui, sans-serif',
                        }"
                    >
                        Monitor your
                        <span
                            class="block mt-2 bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent animate-gradient"
                        >
                            websites and APIs
                        </span>
                        <span class="block mt-2">in real time</span>
                    </h1>

                    <!-- Subtext -->
                    <p
                        class="text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed"
                    >
                        A dead-simple uptime monitor for your side projects.
                        Deploy once, forget about it. Get pinged when things
                        break.
                    </p>

                    <!-- CTA Buttons -->
                    <div
                        class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4"
                    >
                        <button
                            @click="handleDemo"
                            class="group w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-cyan-600 via-teal-600 to-emerald-600 rounded-lg font-semibold text-white hover:shadow-xl hover:shadow-teal-500/30 transition-all flex items-center justify-center gap-2"
                        >
                            <span>Try the Demo</span>
                            <ArrowRightIcon
                                class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"
                            />
                        </button>
                        <button
                            @click="authModal.open('signin')"
                            class="w-full sm:w-auto px-6 py-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg font-semibold hover:bg-white/10 transition-all"
                        >
                            Sign In
                        </button>
                    </div>

                    <!-- Quick Stats -->
                    <div
                        class="flex flex-wrap items-center justify-center gap-6 sm:gap-10 pt-8 text-sm"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    >
                        <div class="flex items-center gap-2">
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-cyan-400"
                            ></div>
                            <span class="text-gray-500">Checks every 30s</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Visual - Terminal-style Dashboard -->
                <div class="relative max-w-4xl mx-auto mt-12">
                    <div
                        class="absolute -inset-6 bg-gradient-to-r from-cyan-500/10 via-teal-500/10 to-emerald-500/10 rounded-2xl blur-2xl"
                    ></div>

                    <div
                        class="relative bg-[#16161E] rounded-xl border border-white/10 overflow-hidden shadow-2xl"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    >
                        <!-- Terminal Header -->
                        <div
                            class="px-4 py-3 bg-[#1A1A24] border-b border-white/10 flex items-center justify-between"
                        >
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1.5">
                                    <div
                                        class="w-3 h-3 rounded-full bg-red-400/80"
                                    ></div>
                                    <div
                                        class="w-3 h-3 rounded-full bg-yellow-400/80"
                                    ></div>
                                    <div
                                        class="w-3 h-3 rounded-full bg-green-400/80"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-500 ml-2"
                                    >pulsecheck dashboard</span
                                >
                            </div>
                            <div
                                class="flex items-center gap-2 text-xs text-gray-500"
                            >
                                <div class="flex items-center gap-1.5">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"
                                    ></div>
                                    <span>live</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dashboard Content -->
                        <div class="p-5 space-y-4 text-sm">
                            <!-- Status Banner -->
                            <div
                                class="flex items-center gap-3 p-3 bg-cyan-500/5 border border-cyan-500/20 rounded-lg"
                            >
                                <div
                                    class="relative flex items-center justify-center"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full bg-cyan-400 animate-ping absolute"
                                    ></div>
                                    <div
                                        class="w-2 h-2 rounded-full bg-cyan-400"
                                    ></div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-cyan-300">
                                        All systems operational
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        Last check: {{ lastChecked }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    5 services monitored
                                </div>
                            </div>

                            <!-- Mini Chart -->
                            <div
                                class="bg-[#1A1A24] rounded-lg p-4 border border-white/5"
                            >
                                <div class="text-xs text-gray-500 mb-3">
                                    Response time (24h)
                                </div>
                                <div class="h-24 relative">
                                    <svg
                                        class="w-full h-full"
                                        viewBox="0 0 400 80"
                                        preserveAspectRatio="none"
                                    >
                                        <defs>
                                            <linearGradient
                                                id="miniGradient"
                                                x1="0"
                                                x2="0"
                                                y1="0"
                                                y2="1"
                                            >
                                                <stop
                                                    offset="0%"
                                                    stop-color="#06b6d4"
                                                    stop-opacity="0.3"
                                                />
                                                <stop
                                                    offset="100%"
                                                    stop-color="#06b6d4"
                                                    stop-opacity="0.0"
                                                />
                                            </linearGradient>
                                        </defs>
                                        <path
                                            :d="chartPath"
                                            fill="url(#miniGradient)"
                                            class="transition-all duration-500"
                                        />
                                        <path
                                            :d="chartLine"
                                            fill="none"
                                            stroke="#06b6d4"
                                            stroke-width="2"
                                            class="transition-all duration-500"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <!-- Services List -->
                            <div class="space-y-2">
                                <div
                                    v-for="service in services"
                                    :key="service.name"
                                    class="flex items-center justify-between p-3 bg-[#1A1A24] rounded-lg border border-white/5 hover:border-cyan-500/30 transition-colors group"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="relative flex items-center justify-center"
                                        >
                                            <div
                                                :class="[
                                                    'w-1.5 h-1.5 rounded-full',
                                                    service.status ===
                                                    'operational'
                                                        ? 'bg-cyan-400 animate-pulse'
                                                        : 'bg-red-400',
                                                ]"
                                            ></div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">
                                                {{ service.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ service.url }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-4 text-right"
                                    >
                                        <div>
                                            <div
                                                class="text-cyan-300 font-semibold"
                                            >
                                                {{ service.uptime }}%
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                uptime
                                            </div>
                                        </div>
                                        <div>
                                            <div
                                                class="text-white font-semibold"
                                            >
                                                {{ service.responseTime }}ms
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                response
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Live Dashboard Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12 space-y-3">
                    <h2
                        class="text-4xl sm:text-5xl font-black"
                        :style="{
                            fontFamily:
                                'Cabinet Grotesk, system-ui, sans-serif',
                        }"
                    >
                        One dashboard.
                        <span
                            class="bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent"
                        >
                            All your services.
                        </span>
                    </h2>
                    <p class="text-lg text-gray-400 max-w-xl mx-auto">
                        See everything at a glance. No clutter, no complexity.
                    </p>
                </div>

                <!-- Simplified Dashboard -->
                <div class="relative">
                    <div
                        class="absolute -inset-4 bg-gradient-to-r from-cyan-500/5 via-teal-500/5 to-emerald-500/5 rounded-3xl blur-3xl"
                    ></div>

                    <div
                        class="relative bg-[#16161E] rounded-2xl border border-white/10 overflow-hidden shadow-2xl"
                        :style="{ fontFamily: 'JetBrains Mono, monospace' }"
                    >
                        <!-- Header -->
                        <div class="p-5 border-b border-white/10">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold mb-1">
                                        Status Board
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Monitoring
                                        {{ detailedServices.length }} endpoints
                                    </p>
                                </div>
                                <div
                                    class="px-3 py-1.5 bg-cyan-500/10 border border-cyan-500/20 rounded-md"
                                >
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="relative flex items-center justify-center"
                                        >
                                            <div
                                                class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping absolute"
                                            ></div>
                                            <div
                                                class="w-1.5 h-1.5 rounded-full bg-cyan-400"
                                            ></div>
                                        </div>
                                        <span class="text-sm text-cyan-300"
                                            >All up</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Chart -->
                        <div class="p-5 border-b border-white/10">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-400">
                                    Response Times
                                </h4>
                                <div class="flex gap-1.5 text-xs">
                                    <button
                                        class="px-2 py-1 bg-cyan-500/20 text-cyan-300 rounded"
                                    >
                                        24h
                                    </button>
                                    <button
                                        class="px-2 py-1 text-gray-500 hover:bg-white/5 rounded transition-colors"
                                    >
                                        7d
                                    </button>
                                    <button
                                        class="px-2 py-1 text-gray-500 hover:bg-white/5 rounded transition-colors"
                                    >
                                        30d
                                    </button>
                                </div>
                            </div>

                            <div
                                class="h-48 bg-[#1A1A24] rounded-lg p-4 relative border border-white/5"
                            >
                                <svg
                                    class="w-full h-full"
                                    viewBox="0 0 800 160"
                                    preserveAspectRatio="none"
                                >
                                    <defs>
                                        <linearGradient
                                            id="mainGradient"
                                            x1="0"
                                            x2="0"
                                            y1="0"
                                            y2="1"
                                        >
                                            <stop
                                                offset="0%"
                                                stop-color="#06b6d4"
                                                stop-opacity="0.2"
                                            />
                                            <stop
                                                offset="100%"
                                                stop-color="#06b6d4"
                                                stop-opacity="0.0"
                                            />
                                        </linearGradient>
                                    </defs>

                                    <!-- Grid -->
                                    <line
                                        v-for="i in 4"
                                        :key="`grid-${i}`"
                                        x1="0"
                                        :y1="i * 40"
                                        x2="800"
                                        :y2="i * 40"
                                        stroke="rgba(255,255,255,0.03)"
                                        stroke-width="1"
                                    />

                                    <path
                                        :d="mainChartPath"
                                        fill="url(#mainGradient)"
                                        class="transition-all duration-700"
                                    />
                                    <path
                                        :d="mainChartLine"
                                        fill="none"
                                        stroke="#06b6d4"
                                        stroke-width="2"
                                        class="transition-all duration-700"
                                    />

                                    <!-- Dot -->
                                    <circle
                                        :cx="animatedDotX"
                                        :cy="animatedDotY"
                                        r="3"
                                        fill="#06b6d4"
                                        class="drop-shadow-lg"
                                    >
                                        <animate
                                            attributeName="r"
                                            values="3;5;3"
                                            dur="2s"
                                            repeatCount="indefinite"
                                        />
                                    </circle>
                                </svg>

                                <!-- Labels -->
                                <div
                                    class="absolute left-2 top-2 bottom-2 flex flex-col justify-between text-xs text-gray-600"
                                >
                                    <span>150ms</span>
                                    <span>100ms</span>
                                    <span>50ms</span>
                                    <span>0ms</span>
                                </div>
                            </div>
                        </div>

                        <!-- Services List -->
                        <div class="p-5 space-y-2">
                            <div
                                v-for="service in detailedServices"
                                :key="service.name"
                                class="flex items-center justify-between p-3 bg-[#1A1A24] rounded-lg border border-white/5 hover:border-cyan-500/30 transition-colors cursor-pointer group"
                            >
                                <div class="flex items-center gap-3 flex-1">
                                    <div
                                        class="relative flex items-center justify-center"
                                    >
                                        <div
                                            :class="[
                                                'w-2 h-2 rounded-full',
                                                service.status === 'operational'
                                                    ? 'bg-cyan-400'
                                                    : 'bg-red-400',
                                            ]"
                                        >
                                            <div
                                                :class="[
                                                    'w-2 h-2 rounded-full animate-ping',
                                                    service.status ===
                                                    'operational'
                                                        ? 'bg-cyan-400'
                                                        : 'bg-red-400',
                                                ]"
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex items-center gap-2 mb-0.5"
                                        >
                                            <div
                                                class="font-medium text-white truncate"
                                            >
                                                {{ service.name }}
                                            </div>
                                            <div
                                                class="px-1.5 py-0.5 text-xs bg-white/5 rounded text-gray-500"
                                            >
                                                {{ service.method }}
                                            </div>
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 truncate"
                                        >
                                            {{ service.url }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div
                                            class="text-sm font-semibold text-cyan-300"
                                        >
                                            {{ service.uptime }}%
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            uptime
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-sm font-semibold text-white"
                                        >
                                            {{ service.responseTime }}ms
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            avg
                                        </div>
                                    </div>
                                    <ChevronRightIcon
                                        class="w-4 h-4 text-gray-600 group-hover:text-gray-400 transition-colors"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12 space-y-3">
                    <h2
                        class="text-4xl sm:text-5xl font-black"
                        :style="{
                            fontFamily:
                                'Cabinet Grotesk, system-ui, sans-serif',
                        }"
                    >
                        Simple monitoring.
                        <span
                            class="bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent"
                        >
                            Zero hassle.
                        </span>
                    </h2>
                    <p class="text-lg text-gray-400 max-w-xl mx-auto">
                        Built for developers who want things to just work.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <!-- Feature 1 -->
                    <div
                        class="group p-6 bg-gradient-to-br from-white/[0.03] to-transparent rounded-xl border border-white/10 hover:border-cyan-500/30 transition-all"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 border border-cyan-500/20 flex items-center justify-center mb-4"
                        >
                            <ChartBarIcon class="w-5 h-5 text-cyan-400" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">
                            Continuous Health Checks
                        </h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Ping your endpoints every 30 seconds. HTTP, HTTPS,
                            TCP, and DNS monitoring out of the box.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="group p-6 bg-gradient-to-br from-white/[0.03] to-transparent rounded-xl border border-white/10 hover:border-teal-500/30 transition-all"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-teal-500/20 to-teal-500/5 border border-teal-500/20 flex items-center justify-center mb-4"
                        >
                            <BellAlertIcon class="w-5 h-5 text-teal-400" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Instant Alerts</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Get notified via email, Slack, Discord, or webhook
                            when something breaks. No noise, just failures.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="group p-6 bg-gradient-to-br from-white/[0.03] to-transparent rounded-xl border border-white/10 hover:border-emerald-500/30 transition-all"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/20 flex items-center justify-center mb-4"
                        >
                            <ChartPieIcon class="w-5 h-5 text-emerald-400" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Historical Data</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Track uptime percentage, response times, and
                            incident history. All stored locally in your
                            database.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="group p-6 bg-gradient-to-br from-white/[0.03] to-transparent rounded-xl border border-white/10 hover:border-cyan-500/30 transition-all"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 border border-cyan-500/20 flex items-center justify-center mb-4"
                        >
                            <CreditCardIcon class="w-5 h-5 text-cyan-400" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Self-Hosted</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Deploy on your own server. Docker-ready, environment
                            variables for config. Your data stays yours.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 relative">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[300px] bg-gradient-to-r from-cyan-500/5 via-teal-500/5 to-emerald-500/5 rounded-full blur-[120px]"
                ></div>
            </div>

            <div class="max-w-4xl mx-auto text-center relative">
                <h2
                    class="text-4xl sm:text-5xl font-black mb-4"
                    :style="{
                        fontFamily: 'Cabinet Grotesk, system-ui, sans-serif',
                    }"
                >
                    Ready to stop wondering
                    <span
                        class="block mt-2 bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent"
                    >
                        if your site is down?
                    </span>
                </h2>

                <p class="text-lg text-gray-400 mb-8 max-w-xl mx-auto">
                    Deploy PulseCheck in 5 minutes. Self-host it, own your data,
                    sleep better at night.
                </p>

                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-10"
                >
                    <button
                        @click="handleDemo"
                        class="group w-full sm:w-auto px-7 py-3.5 bg-gradient-to-r from-cyan-600 via-teal-600 to-emerald-600 rounded-lg font-semibold text-white hover:shadow-xl hover:shadow-teal-500/30 transition-all flex items-center justify-center gap-2"
                    >
                        <span>Try Demo</span>
                        <ArrowRightIcon
                            class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"
                        />
                    </button>
                    <button
                        @click="handleViewDashboard"
                        class="w-full sm:w-auto px-7 py-3.5 bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg font-semibold hover:bg-white/10 transition-all"
                    >
                        View Dashboard
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-white/10 py-10 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-6"
                >
                    <div class="flex items-center gap-2.5">
                        <div class="relative">
                            <div
                                class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 flex items-center justify-center"
                            >
                                <div
                                    class="w-2 h-2 rounded-full bg-white"
                                ></div>
                            </div>
                        </div>
                        <span
                            class="text-lg font-bold"
                            :style="{ fontFamily: 'Space Mono, monospace' }"
                            >PulseCheck</span
                        >
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import DemoModal from "../components/modals/DemoModal.vue";
import AuthModal from "../components/modals/AuthModal.vue";
import { useAuthModalStore } from "@/stores/authModal";
import {
    ArrowRightIcon,
    ChevronRightIcon,
    ChartBarIcon,
    BellAlertIcon,
    ChartPieIcon,
    CreditCardIcon,
} from "@heroicons/vue/24/solid";

const authModal = useAuthModalStore();

// Time display
const lastChecked = ref("2 min ago");

const showDemo = ref(false);

// Service data
const services = ref([
    {
        name: "Production API",
        url: "api.example.com",
        status: "operational",
        uptime: 99.98,
        responseTime: 45,
    },
    {
        name: "Website",
        url: "example.com",
        status: "operational",
        uptime: 100,
        responseTime: 38,
    },
    {
        name: "CDN",
        url: "cdn.example.com",
        status: "operational",
        uptime: 99.95,
        responseTime: 52,
    },
]);

const detailedServices = ref([
    {
        name: "Production API",
        url: "https://api.example.com/health",
        method: "GET",
        status: "operational",
        uptime: 99.98,
        responseTime: 45,
    },
    {
        name: "Marketing Site",
        url: "https://www.example.com",
        method: "HEAD",
        status: "operational",
        uptime: 100,
        responseTime: 38,
    },
    {
        name: "CDN",
        url: "https://cdn.example.com/status",
        method: "GET",
        status: "operational",
        uptime: 99.95,
        responseTime: 52,
    },
    {
        name: "Auth Service",
        url: "https://auth.example.com/ping",
        method: "GET",
        status: "operational",
        uptime: 99.97,
        responseTime: 65,
    },
    {
        name: "Database",
        url: "https://db.example.com/health",
        method: "GET",
        status: "operational",
        uptime: 99.99,
        responseTime: 28,
    },
]);

// Chart data generation
const generateChartData = (points = 20, volatility = 0.3) => {
    const data = [];
    let value = 50 + Math.random() * 30;

    for (let i = 0; i < points; i++) {
        value += (Math.random() - 0.5) * 20 * volatility;
        value = Math.max(20, Math.min(100, value));
        data.push(value);
    }

    return data;
};

const chartData = ref(generateChartData());
const mainChartData = ref(generateChartData(50, 0.2));

// Computed chart paths
const chartPath = computed(() => {
    const width = 400;
    const height = 80;
    const points = chartData.value.length;

    let path = `M 0,${height - chartData.value[0] * 0.7} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - chartData.value[i] * 0.7;
        path += `L ${x},${y} `;
    }

    path += `L ${width},${height} L 0,${height} Z`;
    return path;
});

const chartLine = computed(() => {
    const width = 400;
    const height = 80;
    const points = chartData.value.length;

    let path = `M 0,${height - chartData.value[0] * 0.7} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - chartData.value[i] * 0.7;
        path += `L ${x},${y} `;
    }

    return path;
});

const mainChartPath = computed(() => {
    const width = 800;
    const height = 160;
    const points = mainChartData.value.length;

    let path = `M 0,${height - mainChartData.value[0] * 1.3} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - mainChartData.value[i] * 1.3;
        path += `L ${x},${y} `;
    }

    path += `L ${width},${height} L 0,${height} Z`;
    return path;
});

const mainChartLine = computed(() => {
    const width = 800;
    const height = 160;
    const points = mainChartData.value.length;

    let path = `M 0,${height - mainChartData.value[0] * 1.3} `;

    for (let i = 1; i < points; i++) {
        const x = (i / (points - 1)) * width;
        const y = height - mainChartData.value[i] * 1.3;
        path += `L ${x},${y} `;
    }

    return path;
});

// Animated dot position
const animatedDotX = computed(() => {
    const width = 800;
    const points = mainChartData.value.length;
    return ((points - 1) / (points - 1)) * width;
});

const animatedDotY = computed(() => {
    const height = 160;
    return height - mainChartData.value[mainChartData.value.length - 1] * 1.3;
});

// Animate chart data
let chartInterval;
onMounted(() => {
    chartInterval = setInterval(() => {
        // Shift data and add new point
        chartData.value.shift();
        chartData.value.push(50 + Math.random() * 30);

        mainChartData.value.shift();
        mainChartData.value.push(50 + Math.random() * 30);
    }, 2000);
});

onUnmounted(() => {
    if (chartInterval) clearInterval(chartInterval);
});

// Event handlers
const handleDemo = () => {
    showDemo.value = true;
};

const handleViewDashboard = () => {
    console.log("View Dashboard clicked");
    // Add dashboard navigation logic
};
</script>
