<script setup>
const stack = [
  'Laravel',
  'Sanctum',
  'Inertia',
  'Vue 3',
];
</script>

<template>
  <main class="min-h-screen bg-gray-50 text-gray-900">
    <div class="mx-auto max-w-5xl px-6 py-16">
      <div class="mb-10">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-gray-500">
          Membership Admin
        </p>

        <h1 class="mt-3 text-4xl font-bold tracking-tight">
          Admin base configured successfully
        </h1>

        <p class="mt-4 max-w-2xl text-lg text-gray-600">
          Laravel is serving the application, Inertia is handling the page response,
          and Vue is rendering the frontend.
        </p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div
            v-for="item in stack"
            :key="item"
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
        >
          <p class="text-sm text-gray-500">Enabled</p>
          <h2 class="mt-2 text-lg font-semibold">{{ item }}</h2>
        </div>
      </div>
    </div>
  </main>
</template>