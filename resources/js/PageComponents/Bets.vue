<template>
  <active-filters :prop-filters="filters" :filter-route="filterRoute" />
  <Card class="flex flex-col bg-white">
    <div class="-my-2">
      <div class="min-w-full py-2 align-middle">
        <div class="overflow-hidden rounded-t-md">
          <table class="w-full table-auto border-collapse divide-y-2 divide-black border-b-2 border-black">
            <thead>
              <tr class="rounded-md text-left text-sm text-gray-700">
                <th class="hidden w-4 text-left text-xs uppercase tracking-wider sm:table-cell" />
                <th class="hidden px-2 py-3 text-left text-xs uppercase tracking-wider sm:table-cell">Time</th>
                <th class="px-6 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Event</th>
                <th class="px-6 py-3 text-left text-xs uppercase tracking-wider text-gray-500">selection</th>
                <th class="hidden px-6 py-3 text-left text-xs uppercase tracking-wider sm:table-cell">stake</th>
                <th class="hidden px-6 py-3 text-left text-xs uppercase tracking-wider sm:table-cell">odds</th>
                <th class="px-6 py-3 text-left text-xs uppercase tracking-wider text-gray-500">result</th>
              </tr>
            </thead>
            <tbody class="divide-y-2 divide-black border-b-2 border-black">
              <template v-for="(bet, betKey) in bets.data" :key="betKey">
                <tr :ref="'bet-' + bet.id" :class="[highlighted == bet.id ? ' bg-indigo-200' : '']" class="cursor-pointer odd:bg-gray-200 even:bg-gray-50">
                  <td class="hidden whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900 sm:table-cell">
                    <sport-icon class="h-6 w-6" :name="bet.sport" />
                  </td>
                  <td class="hidden whitespace-nowrap px-2 py-4 text-sm font-medium text-gray-900 dark:text-slate-400 sm:table-cell">
                    {{ moment(bet.date).format('DD MMM HH:mm') }}
                  </td>
                  <td class="px-6 py-4 text-sm dark:text-slate-400" @click="openBetDetail(isStats ? bet.bet_id : bet.id)">
                    {{ bet.event }}
                  </td>
                  <td class="px-6 py-4 text-sm dark:text-slate-400">
                    {{ bet.selection }}
                  </td>
                  <td class="hidden whitespace-nowrap px-6 py-4 text-sm dark:text-slate-400 sm:table-cell">
                    {{ bet.stake }}
                  </td>
                  <td class="hidden whitespace-nowrap px-6 py-4 text-sm dark:text-slate-400 sm:table-cell">
                    {{ bet.odds }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-3 text-sm dark:text-slate-400" @click.prevent="showDropdown(betKey)">
                    <span>
                      <span v-if="activeKeyBet != betKey || activeKeyBetFixture != -1">
                        <span v-if="bet.result" class="flex items-center justify-between">
                          <span class="inline-flex rounded-full px-2 text-xs leading-5" :class="[statusColor(bet.status, 'label')]">
                            {{ bet.result }}
                          </span>
                        </span>
                        <span v-else class="inline-flex rounded-full bg-yellow-100 px-2 text-xs leading-5 text-yellow-800">
                          {{ bet.status }}
                        </span>
                      </span>
                    </span>
                    <select
                      v-show="activeKeyBet == betKey && activeKeyBetFixture == -1"
                      :ref="'bet' + betKey"
                      v-model="bet.status"
                      class="block rounded-md border-gray-300 py-0.5 pl-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                      :class="{
                        'w-full sm:-mr-24 sm:w-24': activeKeyBet == betKey && activeKeyBetFixture == -1,
                      }"
                      @focusout="handleFocusOut"
                      @change.prevent="selectStatus(bet.status)"
                    >
                      <option v-for="(status, key) in statuses" :key="key" class="capitalize" :value="key">
                        {{ key }}
                      </option>
                    </select>
                  </td>
                  <td v-if="bet.bet_fixture && bet.bet_fixture.length > 1">
                    <button @click="toggleAllBets(bet.id)">
                      <template v-if="!openedBets.includes(bet.id)">
                        <ChevronDownIcon class="h-4 w-4" aria-hidden="true" />
                      </template>
                      <template v-else>
                        <ChevronUpIcon class="h-4 w-4" aria-hidden="true" />
                      </template>
                    </button>
                  </td>
                  <td v-else />
                  <td class=w-2 :class="[statusColor(bet.status, 'background')]"></td>
                </tr>
                <template v-if="bet.bet_fixture && bet.bet_fixture.length > 1">
                  <tr v-for="(bet_fixture, betFixtureKey) in bet.bet_fixture" v-show="openedBets.includes(bet_fixture.bet_id)" :key="betFixtureKey" :class="[statusColor(bet_fixture.status, 'border')]" class="cursor-pointer">
                    <td class="hidden whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900 sm:table-cell" />
                    <td class="hidden whitespace-nowrap px-2 py-4 text-sm font-medium text-gray-900 sm:table-cell">
                      {{ moment(bet_fixture.date).format('DD MMM HH:mm') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ bet_fixture.event }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ bet_fixture.selection }}
                    </td>
                    <td class="hidden whitespace-nowrap px-6 py-4 text-sm sm:table-cell">
                      {{ bet_fixture.stake }}
                    </td>
                    <td class="hidden whitespace-nowrap px-6 py-4 text-sm sm:table-cell">
                      {{ bet_fixture.odds }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-500" @click.prevent="showDropdown(betKey, betFixtureKey)">
                      <span>
                        <span v-if="activeKeyBet != betKey || activeKeyBetFixture != betFixtureKey">
                          <span v-if="bet_fixture.result" class="flex items-center justify-between">
                            <span class="inline-flex rounded-full px-2 text-xs leading-5" :class="[statusColor(bet_fixture.status, 'label')]">
                              {{ bet_fixture.result }}
                            </span>
                          </span>
                          <span v-else class="inline-flex rounded-full px-2 text-xs leading-5" :class="[statusColor(bet_fixture.status, 'label')]">
                            {{ bet_fixture.status }}
                          </span>
                        </span>
                      </span>
                      <select
                        v-show="activeKeyBet == betKey && activeKeyBetFixture == betFixtureKey"
                        :ref="'bet' + betKey + betFixtureKey"
                        v-model="bet_fixture.status"
                        class="block rounded-md border-gray-300 py-0.5 pl-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                        :class="{
                          'w-full sm:-mr-24 sm:w-24': activeKeyBet == betKey && activeKeyBetFixture == betFixtureKey,
                        }"
                        @focusout="handleFocusOut"
                        @change.prevent="selectStatus(bet_fixture.status)"
                      >
                        <option v-for="(status, key) in statuses" :key="key" class="capitalize" :value="key">
                          {{ key }}
                        </option>
                      </select>
                    </td>
                    <td />
                  </tr>
                </template>
              </template>
            </tbody>
          </table>
          <div v-if="bets.data.length == 0" class="col-span-1 whitespace-nowrap bg-white px-6 py-4 text-center text-sm font-medium text-gray-900 dark:bg-slate-800 dark:text-slate-400">No results</div>
        </div>
      </div>
    </div>
    <pagination :data="bets" />
  </Card>
</template>

<script>
import Layout from '@/Layouts/Authenticated.vue'
import { Inertia } from '@inertiajs/inertia'
import TextInput from '@/Components/TextInput.vue'
import TextInputWithAddOn from '@/Components/TextInputWithAddOn.vue'
import Pagination from '@/PageComponents/Pagination.vue'
import ActiveFilters from '@/PageComponents/ActiveFilters.vue'
import moment from 'moment'
import Button from '@/Components/Button.vue'
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'
import SportIcon from '@/Components/SportIcon.vue'
import emitter from '@/Plugins/mitt'
import Card from '@/PageComponents/Card.vue'

export default {
  components: {
    TextInput,
    TextInputWithAddOn,
    Pagination,
    ActiveFilters,
    Button,
    ChevronUpIcon,
    ChevronDownIcon,
    SportIcon,
    Card,
  },
  layout: Layout,

  props: {
    bets: Object,
    errors: Object,
    filters: Array,
    filterRoute: String,
    filterButton: {
      type: Boolean,
      default: false,
    },
    isStats: {
      default: false,
      type: Boolean,
    },
  },

  data() {
    return {
      betData: this.bets.data,
      selectedStatus: false,
      activeKeyBet: -1,
      activeKeyBetFixture: -1,
      statuses: {
        new: {
          border: 'dark:border-slate-200/20',
          label: 'bg-yellow-100 text-yellow-800',
          background: 'bg-gray-300',
        },
        won: {
          border: 'border-green-500',
          label: 'bg-green-100 text-green-800',
          background: 'bg-green-500',
        },
        lost: {
          border: 'border-red-500',
          label: 'bg-red-100 text-red-800',
          background: 'bg-red-500',
        },
        void: {
          border: 'border-gray-300 dark:border-slate-200/20',
          label: 'bg-gray-100 text-gray-800',
          background: 'bg-gray-300',
        },
        halfwon: {
          border: 'border-green-500',
          label: 'bg-green-100 text-green-800',
          background: 'bg-green-500',
        },
        halflost: {
          border: 'border-red-500',
          label: 'bg-red-100 text-red-800',
          background: 'bg-red-500',
        },
      },
      localFilters: {},
      highlighted: null,
      openedBets: [],
    }
  },

  created() {
    this.moment = moment
    emitter.on('event:scroll', (id) => {
      this.scrollAndHighlight(id)
    })
  },

  methods: {
    showDropdown(betKey, betFixtureKey = -1) {
      if (!this.$page.props.userInfo.myPage || this.isStats) {
        return
      }
      this.activeKeyBet = betKey
      this.activeKeyBetFixture = betFixtureKey
      var extraKey = ''
      if (betFixtureKey != '-1') {
        extraKey = betFixtureKey
      }
      this.$nextTick(() => this.$refs['bet' + betKey + extraKey].focus())
    },

    handleFocusOut() {
      this.activeKeyBet = -1
      this.activeKeyBetFixture = -1
    },

    openBetDetail(id) {
      if (!this.$page.props.userInfo.myPage || this.activeKeyBet != -1) {
        return
      }

      Inertia.visit(this.route('bet.show', id), {
        method: 'get',
        data: {
          backUrl: window.location.href,
        },
      })
    },

    statusColor(status, type) {
      return this.statuses[status][type]
    },

    selectStatus(selectedStatus) {
      var currentBet = this.bets.data[this.activeKeyBet]
      var status = ''
      if (this.activeKeyBetFixture == -1) {
        if (selectedStatus == 'won') {
          status = '+' + (currentBet.stake * currentBet.odds).toFixed(2)
        } else if (selectedStatus == 'lost') {
          status = -currentBet.stake
        } else if (selectedStatus == 'new') {
          status = null
        } else if (selectedStatus == 'void') {
          status = 0
        } else if (selectedStatus == 'halfwon') {
          status = '+' + (currentBet.stake / 2 + currentBet.odds / 2).toFixed(2)
        } else if (selectedStatus == 'halflost') {
          status = '-' + (currentBet.stake / 2).toFixed(2)
        }

        //currentBet?
        currentBet.result = status
        currentBet.status = selectedStatus
      } else {
        currentBet = currentBet.bet_fixture[this.activeKeyBetFixture]
        currentBet.status = selectedStatus
      }

      Inertia.put(this.route('bet.updateStatus'), currentBet, {
        preserveScroll: true,
      })

      setTimeout(() => {
        this.activeKeyBet = -1
        this.activeKeyBetFixture = -1
      }, 1)
    },

    findPage(id) {
      this.$http.get(this.route('bet.pageNumber', id)).then((response) => {
        if (response.data) {
          Inertia.reload({
            data: {
              page: response.data,
            },
            onSuccess: () => {
              const el = this.$refs['bet-' + id]
              if (el) {
                this.scroll(id, el)
              }
            },
          })
        }
      })
    },

    scroll(id, el) {
      this.highlighted = id
      setTimeout(() => {
        this.highlighted = null
      }, 3000)
      el[0].scrollIntoView({ behavior: 'smooth' })
    },

    scrollAndHighlight(id) {
      const el = this.$refs['bet-' + id]
      if (!el || el.length == 0) {
        this.findPage(id)
      } else {
        this.scroll(id, el)
      }
    },

    toggleAllBets(id) {
      const index = this.openedBets.indexOf(id)
      if (index > -1) {
        this.openedBets.splice(index, 1)
      } else {
        this.openedBets.push(id)
      }
    },
  },
}
</script>
