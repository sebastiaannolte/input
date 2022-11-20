<template>
  <Head :title="bet.event" />
  <Layout :title="bet.event" :errors="errors">
    <div class="mb-4 w-full rounded-md bg-white p-4 px-4 text-right leading-4 shadow dark:bg-slate-800 sm:overflow-hidden sm:text-center">
      <div class="pb-5 sm:flex">
        <div class="hidden sm:block">
          <ButtonLink :href="backUrl"> <ArrowNarrowLeftIcon class="mr-1.5 h-4 w-4" aria-hidden="true" /> All bets</ButtonLink>
        </div>
        <div class="flex flex-grow flex-col-reverse sm:flex-row">
          <h2 id="payment-details-heading" class="flex-1 p-2 text-left text-2xl font-bold leading-6 text-gray-900 dark:text-slate-200">
            {{ bet.event }}
          </h2>
          <div class="flex justify-between">
            <div>
              <ButtonLink class="sm:hidden" :href="backUrl"> <ArrowNarrowLeftIcon class="mr-1.5 h-4 w-4" aria-hidden="true" /> All bets</ButtonLink>
            </div>
            <div>
              <button class="mr-2 inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2" @click="destroy">Delete</button>
              <button class="inline-flex justify-center rounded-md border border-transparent bg-red-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2" @click="editBet">Edit</button>
            </div>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-8 gap-4 text-left dark:text-slate-400">
        <div class="col-span-8 sm:col-span-4">
          <div class="p-2 dark:text-slate-200">Event</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.event }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Selection</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.selection }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Category</div>
          <div class="mb-2 p-2 font-bold">
            {{ categoryName }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Bookie</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.bookie }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Stake</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.stake }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Odds</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.odds }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Result</div>
          <div class="mb-2 p-2 font-bold">
            {{ betResult }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Date</div>
          <div class="mb-2 p-2 font-bold">
            {{ moment(bet.date).format('YYYY-MM-DD HH:mm') }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Tipster</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.tipster }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Status</div>
          <div class="mb-2 p-2 font-bold">
            {{ bet.status }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Type</div>
          <div class="mb-2 p-2 font-bold capitalize">
            {{ bet.type }}
          </div>
        </div>
        <div class="col-span-4 sm:col-span-2">
          <div class="p-2 dark:text-slate-200">Placed on</div>
          <div class="mb-2 p-2 font-bold capitalize">
            {{ moment(bet.created_at).format('YYYY-MM-DD HH:mm') }}
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
export default {
  created() {
    // this.moment = moment
  },
  methods: {},
  components: { ButtonLink },
}
</script>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import { ArrowNarrowLeftIcon } from '@heroicons/vue/outline'
import moment from 'moment'
import emitter from '@/Plugins/mitt'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ButtonLink from '@/Components/ButtonLink.vue'

const props = defineProps({
  bet: Object,
  errors: Object,
})

const backUrl = ref(route('userhome', usePage().props.value.userInfo.user.username))

var urlParams = new URLSearchParams(window.location.search)
if (urlParams.get('backUrl')) {
  backUrl.value = urlParams.get('backUrl')
}

const betResult = computed(() => props.bet.result ?? 'No result')

const categoryName = computed(() => {
  if (!props.bet.category) {
    return
  }
  var categories = props.bet.category.split(', ')
  var categoryNames = []
  for (var key in categories) {
    var value = categories[key]
    categoryNames.push(usePage().props.value.betTypes[props.bet.sport].find((betType) => betType.id == value).name)
  }
  return categoryNames.join(', ')
})

const editBet = () => {
  emitter.emit('event:edit', Object.assign({}, props.bet))
}

const destroy = () => {
  if (confirm('Are you sure you want to delete this bet?')) {
    Inertia.delete(route('bet.delete', props.bet.id))
  }
}
</script>
