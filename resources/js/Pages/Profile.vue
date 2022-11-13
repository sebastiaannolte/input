<template>
  <Layout title="Profile" :errors="errors">
    <Settings>
      <div class="n">
        <div class="py-6 px-4 sm:p-6">
          <div>
            <h2
              id="payment-details-heading"
              class="text-xl leading-6 font-medium text-gray-900 font-bold"
            >
              Profile
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              The default setting are used when adding new bets
            </p>
          </div>
          <div class="mt-6 grid grid-cols-4 gap-4">
            <div class="col-span-4 sm:col-span-2">
              <text-input
                v-model="userData.username"
                :error="errors.username"
                label="Username"
              />
            </div>

            <div class="col-span-4 sm:col-span-2">
              <text-input
                v-model="userData.name"
                :error="errors.name"
                label="Name"
              />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <text-input
                v-model="userData.email"
                :error="errors.email"
                label="Email"
              />
            </div>
          </div>
        </div>
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex justify-between">
          <inertia-link
            href="/logout"
            method="post"
            as="button"
            class="
          mr-2
          bg-white
          border border-gray-300
          rounded-md
          shadow-sm
          py-2
          px-4
          inline-flex
          justify-center
          text-sm
          font-medium
          text-black
          hover:bg-gray-100
          focus:outline-none
          focus:ring-2
          focus:ring-offset-2
          focus:ring-gray-300
        "
          >
            Logout
          </inertia-link>
          <loading-button :loading="userData.processing" @click.prevent="save">
            Save
          </loading-button>
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

const userData =  useForm(usePage().props.value.auth.user)

const save = () => {
  userData.put(
    route('profile.update', usePage().props.value.auth.user.username),
    userData,
    {
      preserveScroll: true,
    },
  )
}
</script>
