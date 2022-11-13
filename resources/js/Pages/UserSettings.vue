<template>
  <Layout title="Settings" :errors="errors">
    <Settings>
      <form action="#" method="POST">
        <div class="">
          <div class="py-6 px-4 sm:p-6 h-96">
            <settings-tabs />
            <div>
              <h2 class="text-xl leading-6 font-medium text-gray-900 font-bold">
                Settings
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                The settings are used when adding new bets
              </p>
            </div>
            <div class="mt-6 grid grid-cols-4 gap-4">
              <div
                v-for="(value, key) in stringSettings"
                :key="key"
                class="col-span-4 sm:col-span-2"
              >
                <text-input
                  v-model="newSettings[key].value"
                  class="
                mt-1
                block
                w-full
                border border-gray-300
                rounded-md
                shadow-sm
                py-2
                px-3
                focus:outline-none focus:ring-gray-900 focus:border-gray-900
                sm:text-sm
              "
                  :label="key"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <label
                  class="
                block
                text-sm
                font-medium
                text-gray-700
                dark:text-gray-400
                capitalize
              "
                >Games:</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <div
                    class="relative flex items-stretch flex-grow focus-within:z-10"
                  >
                    <div
                      class="
                    absolute
                    inset-y-0
                    left-0
                    pl-3
                    flex
                    items-center
                    pointer-events-none
                  "
                    />
                    <input
                      id="email"
                      v-model="date"
                      type="date"
                      class="
                    focus:ring-indigo-500 focus:border-indigo-500
                    block
                    w-full
                    rounded-none rounded-l-md
                    pl-3
                    sm:text-sm
                    border-gray-300
                  "
                    />
                  </div>
                  <button
                    v-for="sport in sports"
                    :key="sport.name"
                    type="button"
                    :class="{
                      'bg-gray-200 text-white inner-shadow':
                        activeSport == sport.name,
                    }"
                    class="
                  w-10
                  h-10
                  inline-flex
                  items-center
                  px-1.5
                  py-1.5
                  border border-gray-300
                  shadow-sm
                  text-xs
                  font-medium
                  rounded
                  text-gray-700
                  bg-white
                  hover:bg-gray-50
                  focus:outline-none focus:bg-gray-300
                "
                    @click="setSportType(sport.name)"
                  >
                    <sport-icon class="w-6 h-6" :name="sport.name" />
                  </button>
                  <loading-button
                    :loading="loadingGames"
                    class="rounded-l-none"
                    @click.prevent="getGames"
                  >
                    <span>Get games</span>
                  </loading-button>
                </div>
                <span>{{ gamesResponse }}</span>
              </div>
              <div class="col-span-4 sm:col-span-2">
                <label
                  class="
                block
                text-sm
                font-medium
                text-gray-700
                dark:text-gray-400
                capitalize
              "
                >Bookies:</label>
                <Multiselect
                  v-model="newSettings.bookmakers.value"
                  mode="tags"
                  :searchable="true"
                  :create-tag="true"
                  add-tag-on="'enter'|'space'|'tab'|';'|','"
                  :options="bookmakerNames"
                />
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <loading-button
              :loading="newSettings.processing"
              @click.prevent="saveSettings"
            >
              Save
            </loading-button>
          </div>
        </div>
      </form>
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
  return props.bookmakers
    .map((el) => el.name)
    .sort((a, b) => a.localeCompare(b))
})
const stringSettings = computed(() => {
  return Object.fromEntries(
    Object.entries(settings.value).filter(
      ([key, value]) => value.type == 'string',
    ),
  )
})

date.value = moment().format('YYYY-MM-DD')
settings.value =usePage().props.value.auth.settings
newSettings.value = useForm(settings.value)

const saveSettings = () => {
  newSettings.value.post(
    route('userSettings.store', usePage().props.value.auth.user.username),
    {
      preserveScroll: true, // bets are not added to frontend
    },
  )
}

const setSportType = (type) => {
  activeSport.value = type
}

const getGames = () => {
  loadingGames.value = true
  axios
    .get(route('games.get', [date.value, activeSport.value]))
    .then((response) => {
      loadingGames.value = false
      if (response.data) {
        gamesResponse.value = response.data.message
      }
    })
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
