<template>
  <Layout title="Settings" :errors="errors">
    <Settings>
      <div class="">
        <div class="h-96 py-6 px-4 sm:p-6">
          <settings-tabs />
          <div>
            <h2 class="text-xl font-bold leading-6 text-gray-900 dark:text-slate-200">Settings</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">The settings are used when adding new bets</p>
          </div>
          <div class="mt-6 grid grid-cols-4 gap-4">
            <div v-for="(value, key) in stringSettings" :key="key" class="col-span-4 sm:col-span-2">
              <text-input v-model="newSettings[key].value" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm" :label="key" />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <label class="block text-sm font-medium capitalize text-gray-700 dark:text-slate-400">Games:</label>
              <button
                v-for="sport in sports"
                :key="sport.name"
                type="button"
                :class="{
                  'inner-shadow bg-gray-200 text-white': activeSport == sport.name,
                }"
                class="inline-flex h-10 w-10 items-center rounded border border-gray-300 bg-white px-1.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none"
                @click="setSportType(sport.name)"
              >
                <sport-icon class="h-6 w-6" :name="sport.name" />
              </button>
              <div class="mt-1 flex rounded-md shadow-sm">
                <div class="relative flex flex-grow items-stretch focus-within:z-10">
                  <MultiDatePicker v-model="date" />
                </div>

                <loading-button :loading="loadingGames" class="rounded-l-none" @click.prevent="getGames">
                  <span>Get games</span>
                </loading-button>
              </div>
              <span v-html="gamesResponse" />
            </div>
            <div class="col-span-4 sm:col-span-2">
              <label class="block text-sm font-medium capitalize text-gray-700 dark:text-slate-400">Bookies:</label>
              <Multiselect v-model="newSettings.bookmakers.value" mode="tags" :searchable="true" :create-tag="true" add-tag-on="'enter'|'space'|'tab'|';'|','" :options="bookmakerNames" />
            </div>
            <div class="col-span-4 sm:col-span-2">
              Theme:<br />
              <DarkModeToggle />
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right dark:border-t dark:border-slate-200/5 dark:bg-slate-800 sm:px-6">
          <loading-button :loading="newSettings.processing" @click.prevent="saveSettings"> Save </loading-button>
        </div>
      </div>
    </Settings>
  </Layout>
</template>
<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Components/LoadingButton.vue'
import Settings from '@/Layouts/Settings.vue'
import moment from 'moment'
import Multiselect from '@vueform/multiselect'
import SportIcon from '@/Components/SportIcon.vue'
import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import MultiDatePicker from '@/Components/MultiDatePicker.vue'
import DarkModeToggle from '@/Components/DarkModeToggle.vue'

const props = defineProps({
  bookmakers: Object,
  errors: Object,
})

const settings = ref({})
const newSettings = ref({})
const date = ref(null)
const gamesResponse = ref(null)
const loadingGames = ref(false)
const speiclaTabsFiltered = ref(null)
const activeSport = ref('football')
const sports = computed(() => usePage().props.value.sports)
const bookmakerNames = computed(() => {
  return props.bookmakers.map((el) => el.name).sort((a, b) => a.localeCompare(b))
})
const stringSettings = computed(() => {
  return Object.fromEntries(Object.entries(settings.value).filter(([key, value]) => value.type == 'string'))
})

settings.value = usePage().props.value.auth.settings
newSettings.value = useForm(settings.value)

const saveSettings = () => {
  newSettings.value.post(route('userSettings.store', usePage().props.value.auth.user.username), {
    preserveScroll: true, // bets are not added to frontend
  })
}

const setSportType = (type) => {
  activeSport.value = type
}

const getGames = () => {
  loadingGames.value = true
  axios.get(route('games.get', [JSON.stringify(date.value.map((item) => item.date)), activeSport.value])).then((response) => {
    loadingGames.value = false
    if (response.data) {
      gamesResponse.value = response.data.message
    }
  })
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
