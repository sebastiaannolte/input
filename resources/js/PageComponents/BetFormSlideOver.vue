<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="fixed inset-0 z-20 overflow-hidden" @close="open = false">
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 right-0 flex max-w-full pl-16">
          <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
            <div class="w-screen max-w-md">
              <form class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl dark:divide-slate-200/5 dark:bg-slate-800">
                <div class="h-0 flex-1 overflow-y-auto">
                  <div class="bg-indigo-700 py-6 px-4 sm:px-6">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white dark:text-slate-200">
                        {{ title }}
                      </DialogTitle>
                      <div class="ml-3 flex h-7 items-center">
                        <button type="button" class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white" @click="open = false">
                          <span class="sr-only">Close panel</span>
                          <XIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                      </div>
                    </div>
                    <div class="mt-1">
                      <p class="text-sm text-indigo-300 dark:text-slate-400">Get started by filling in the information below to create your new project.</p>
                    </div>
                  </div>
                  <div class="space-y-6 lg:col-span-9">
                    <section aria-labelledby="payment-details-heading">
                      <form action="#" method="POST">
                        <div class="">
                          <div class="py-6 px-4 sm:p-6">
                            <div class="grid grid-cols-4 gap-4">
                              <span class="col-span-4">
                                <button
                                  v-for="sport in sports"
                                  :key="sport.name"
                                  type="button"
                                  :class="{
                                    'inner-shadow bg-gray-200 text-white dark:hover:bg-slate-400/20': activeSport == sport.name,
                                  }"
                                  class="mr-2 inline-flex h-10 w-10 items-center rounded border border-gray-300 bg-white px-1.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none dark:border-slate-200/5 dark:bg-slate-800"
                                  @click="setSportType(sport.name)"
                                >
                                  <sport-icon class="h-6 w-6" :name="sport.name" />
                                </button>
                              </span>
                              <div v-if="renderComponent" class="col-span-4 sm:col-span-4">
                                <Event v-for="(game, key) in addedGames" :key="key" v-model="games[key]" :errors="errors" :name="index" :game="game" :index="key" :is-edit="isEdit" :sport="activeSport" />
                                <div class="flex justify-end">
                                  <button class="inline-flex items-center rounded border border-transparent bg-indigo-600 px-1.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click.prevent="addGame">
                                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                                  </button>
                                </div>
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <text-input-with-add-on id="stake" v-model="betData.stake" :error="errors.stake" add-on="units" label="Stake" type="text" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm" />
                              </div>

                              <div class="col-span-4 sm:col-span-2">
                                <text-input id="odds" v-model="betData.odds" :error="errors.odds" label="Odds" type="text" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm" />
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <autocomplete-input id="bookie" v-model="betData.bookie" :options="$page.props.auth.settings.bookmakers.value" :error="errors.bookie" label="Bookie" type="text" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm" />
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <SelectInput v-model="betData.type" :error="errors.type" label="Type">
                                  <option v-for="item in ['Prematch', 'Inplay']" :key="key" class="capitalize dark:bg-slate-700 dark:text-slate-400" :value="item.toLowerCase()">
                                    {{ item }}
                                  </option>
                                </SelectInput>
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <text-input id="tipster" v-model="betData.tipster" :error="errors.tipster" label="Tipster" type="text" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </section>
                  </div>
                </div>
                <div class="flex flex-shrink-0 justify-end px-4 py-4">
                  <div v-if="!bet" class="mr-5 flex items-center">
                    <div class="text-sm">
                      <label for="comments" class="mr-2 font-medium text-gray-700 dark:text-slate-400">Clear inputs</label>
                    </div>
                    <div class="flex h-5 items-center">
                      <input id="comments" v-model="betData.clearInputs" aria-describedby="comments-description" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    </div>
                  </div>
                  <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-slate-200/5 dark:bg-slate-400/10 dark:text-slate-400" @click="open = false">Cancel</button>
                  <loading-button class="ml-5" :loading="betData.processing" @click.prevent="save"> Save </loading-button>
                </div>
              </form>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
export default {
  computed: {
    sports() {
      return this.$page.props.sports
    },
  },
  watch: {
    open: function (newOpen, oldOpen) {
      if (newOpen == false) {
        this.bet = null
      }
    },
  },
  components: { SelectInput },
}
</script>

<script setup>
import Button from '@/Components/Button.vue'
import Event from '@/PageComponents/Event.vue'
import TextInput from '@/Components/TextInput.vue'
import AutocompleteInput from '@/Components/AutocompleteInput.vue'
import LoadingButton from '@/Components/LoadingButton.vue'
import Dropdown from '@/Components/Dropdown.vue'
import TextInputWithAddOn from '@/Components/TextInputWithAddOn.vue'

import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XIcon, PlusIcon } from '@heroicons/vue/outline'
import { onMounted, ref } from 'vue'
import SportIcon from '@/Components/SportIcon.vue'
import emitter from '@/Plugins/mitt'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import SelectInput from '@/Components/SelectInput.vue'

defineProps({
  errors: Object,
})

const open = ref(false)

const bet = ref(null)
const betData = ref({})
const title = ref(null)
const addedGames = ref({ 0: 0 })
const currentGameId = ref(0)
const games = ref({})
const renderComponent = ref(true)
const isEdit = ref(false)
const activeSport = ref('football')

emitter.on('betForm:show', () => {
  open.value = true
  isEdit.value = bet.value && Object.keys(bet.value).length > 0 && bet.value.id
  if (!bet.value) {
    // Reset games if slide over is opened
    games.value = {}
    addedGames.value = { 0: 0 }
  }
  setBetData()
})

emitter.on('game:delete', (index) => {
  delete addedGames.value[index]
  delete games.value[index]
})

emitter.on('event:search', (event) => {
  betData.value.event = event.event
})

emitter.on('event:edit', (event) => {
  bet.value = event
  activeSport.value = bet.value.sport
  currentGameId.value = bet.value.bet_fixture.length - 1
  addedGames.value = Object.assign({}, bet.value.bet_fixture)
  emitter.emit('betForm:show')
})

emitter.on('event:import', (event) => {
  bet.value = event
  currentGameId.value = bet.value.games.length - 1
  addedGames.value = Object.assign({}, bet.value.games)
  emitter.emit('betForm:show')
})

emitter.on('event:clear', (event) => {
  betData.value = useForm({
    bookie: null,
    tipster: null,
    odds: null,
    stake: null,
    sport: null,
    type: null,
    clearInputs: true,
  })
  setUserSettings()
})

const setBetData = () => {
  if (!bet.value) {
    title.value = 'New bet'
    betData.value = useForm({
      bookie: null,
      tipster: null,
      sport: activeSport.value,
      odds: null,
      stake: null,
      type: null,
      games: null,
      clearInputs: true,
    })
    setUserSettings()
  } else if (bet.value.id) {
    bet.value.games = {}
    betData.value = useForm(bet.value)
    title.value = 'Edit ' + bet.value.event
  } else {
    bet.value.games = {}
    betData.value = useForm(
      Object.assign(
        {
          bookie: null,
          tipster: null,
          sport: activeSport.value,
          odds: null,
          stake: null,
          type: null,
          games: null,
          clearInputs: true,
        },
        bet.value,
      ),
    )
    title.value = 'New bet'
  }
}

const setUserSettings = () => {
  var userSettings = usePage().props.value.auth.settings
  for (const key in userSettings) {
    var setting = userSettings[key]
    betData.value[key] = setting.value
  }
}

const setSportType = (type) => {
  activeSport.value = type
  betData.value.sport = type
}

const addGame = () => {
  currentGameId.value++
  addedGames.value[currentGameId.value] = 0
}

const save = () => {
  var router = ''
  if (bet.value && bet.value.id) {
    router = route('bet.update')
    betData.value.games = games.value

    betData.value.put(router, {
      preserveScroll: true,
      onSuccess: () => {
        setBetData()
        open.value = false
        emitter.emit('event:clear')
      },
    })
  } else {
    var importId = betData.value.importId
    router = route('bet.store')
    betData.value.games = games.value
    betData.value.post(router, {
      preserveScroll: true,
      onSuccess: () => {
        if (betData.value.clearInputs) {
          setBetData()
          open.value = false
          emitter.emit('event:clear')
        }

        if (importId > 0) {
          axios
            .put(route('import.update'), {
              id: importId,
            })
            .then((response) => {
              Inertia.visit(route('import.index'))
            })
        }
      },
    })
  }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
