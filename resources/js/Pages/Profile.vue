<template>
  <Layout title="Profile" :errors="errors">
    <Settings>
      <div class="n">
        <div class="py-6 px-4 sm:p-6">
          <div>
            <h2 id="payment-details-heading" class="text-xl font-bold leading-6 text-gray-900 dark:text-slate-200">Profile</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">The default setting are used when adding new bets</p>
          </div>
          <div class="mt-6 grid grid-cols-4 gap-4">
            <div class="col-span-4 sm:col-span-2">
              <text-input v-model="userData.username" :error="errors.username" label="Username" />
            </div>

            <div class="col-span-4 sm:col-span-2">
              <text-input v-model="userData.name" :error="errors.name" label="Name" />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <text-input v-model="userData.email" :error="errors.email" label="Email" />
            </div>
          </div>
        </div>
        <div class="flex justify-between bg-gray-50 px-4 py-3 text-right dark:border-t dark:border-slate-200/5 dark:bg-slate-800 sm:px-6">
          <inertia-link href="/logout" method="post" as="button" class="mr-2 inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 dark:border-slate-200/20 dark:bg-slate-400/10 dark:text-slate-400 dark:hover:bg-slate-400/20"> Logout </inertia-link>
          <loading-button :loading="userData.processing" @click.prevent="save"> Save </loading-button>
        </div>
      </div>
    </Settings>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Components/LoadingButton.vue'
import Settings from '@/Layouts/Settings.vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'

defineProps({
  errors: Object,
})

const userData = useForm(usePage().props.value.auth.user)

const save = () => {
  userData.put(route('profile.update', usePage().props.value.auth.user.username), userData, {
    preserveScroll: true,
  })
}
</script>
