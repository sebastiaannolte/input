<template>
  <active-filters :prop-filters="filters" :filter-route="filterRoute" />
  <div class="flex flex-col">
    <div class="-my-2">
      <div class="py-2 align-middle min-w-full">
        <div
          class="shadow overflow-hidden border-b border-gray-200 rounded-md"
        >
          <table class="table-auto border-collapse w-full">
            <thead class="bg-gray-50 border-gray-50 border-r-8">
              <tr
                class="rounded-md text-sm font-medium text-gray-700 text-left"
              >
                          <th
                  class="
                  w-4

                    hidden
                    sm:table-cell
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >

                </th>
                <th
                  class="
                    px-2
                    py-3
                    hidden
                    sm:table-cell
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Time
                </th>
                <th
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Event
                </th>
                <th
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  selection
                </th>
                <th
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                    hidden
                    sm:table-cell
                  "
                >
                  stake
                </th>
                <th
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                    hidden
                    sm:table-cell
                  "
                >
                  odds
                </th>
                <th
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  result
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(bet, betKey) in bets.data" :key="betKey">
                <tr
                  :ref="'bet-' + bet.id"
                  :class="[
                    betKey % 2 === 0 ? 'bg-white ' : 'bg-gray-50 ',
                    highlighted == bet.id ? ' bg-indigo-200' : '',
                    statusColor(bet.status, 'border'),
                  ]"
                  class="cursor-pointer border-r-8"
                >
                             <td
                    class="
                      hidden
                      sm:table-cell
                      px-2
                      py-2
                      whitespace-nowrap
                      text-sm
                      font-medium
                      text-gray-900
                    "
                  >
                  <sport-icon class="w-6 h-6" :name="bet.sport"/>
                  </td>
                  <td
                    class="
                      hidden
                      sm:table-cell
                      px-2
                      py-4
                      whitespace-nowrap
                      text-sm
                      font-medium
                      text-gray-900
                    "
                  >
                    {{ moment(bet.date).format("DD MMM HH:mm") }}
                  </td>
                  <td
                    class="px-6 py-4 text-sm text-gray-500"
                    @click="openBetDetail(isStats ? bet.bet_id : bet.id)"
                  >
                    {{ bet.event }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">
                    {{ bet.selection }}
                  </td>
                  <td
                    class="
                      px-6
                      py-4
                      whitespace-nowrap
                      text-sm text-gray-500
                      hidden
                      sm:table-cell
                    "
                  >
                    {{ bet.stake }}
                  </td>
                  <td
                    class="
                      px-6
                      py-4
                      whitespace-nowrap
                      text-sm text-gray-500
                      hidden
                      sm:table-cell
                    "
                  >
                    {{ bet.odds }}
                  </td>
                  <td
                    class="px-6 py-3 whitespace-nowrap text-sm text-gray-500"
                    @click.prevent="showDropdown(betKey)"
                  >
                    <span>
                      <span
                        v-if="
                          activeKeyBet != betKey || activeKeyBetFixture != -1
                        "
                      >
                        <span
                          v-if="bet.result"
                          class="flex justify-between items-center"
                        >
                          <span
                            class="
                              px-2
                              inline-flex
                              text-xs
                              leading-5
                              rounded-full
                            "
                            :class="[statusColor(bet.status, 'label')]"
                          >
                            {{ bet.result }}
                          </span>
                        </span>
                        <span
                          v-else
                          class="
                            px-2
                            inline-flex
                            text-xs
                            leading-5
                            rounded-full
                            bg-yellow-100
                            text-yellow-800
                          "
                        >
                          {{ bet.status }}
                        </span>
                      </span>
                    </span>
                    <select
                      @focusout="handleFocusOut"
                      class="
                        block
                        pl-3
                        py-0.5
                        text-base
                        border-gray-300
                        focus:outline-none
                        focus:ring-indigo-500
                        focus:border-indigo-500
                        sm:text-sm
                        rounded-md
                      "
                      :class="{
                        'w-full sm:w-24 sm:-mr-24':
                          activeKeyBet == betKey && activeKeyBetFixture == -1,
                      }"
                      @change.prevent="selectStatus(bet.status)"
                      v-show="
                        activeKeyBet == betKey && activeKeyBetFixture == -1
                      "
                      v-model="bet.status"
                      :ref="'bet' + betKey"
                    >
                      <option
                        class="capitalize"
                        v-for="(status, key) in this.statuses"
                        :key="key"
                        :value="key"
                      >
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
                  <td v-else></td>
                </tr>
                <template v-if="bet.bet_fixture && bet.bet_fixture.length > 1">
                  <tr
                    v-for="(bet_fixture, betFixtureKey) in bet.bet_fixture"
                    :key="betFixtureKey"
                    :class="[statusColor(bet_fixture.status, 'border')]"
                    class="cursor-pointer border-r-8 bg-gray-100"
                    v-show="openedBets.includes(bet_fixture.bet_id)"
                  >
                  <td
                    class="
                      hidden
                      sm:table-cell
                      px-2
                      py-2
                      whitespace-nowrap
                      text-sm
                      font-medium
                      text-gray-900
                    "
                  >
                  </td>
                    <td
                      class="
                        hidden
                        sm:table-cell
                        px-2
                        py-4
                        whitespace-nowrap
                        text-sm
                        font-medium
                        text-gray-900
                      "
                    >
                      {{ moment(bet_fixture.date).format("DD MMM HH:mm") }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ bet_fixture.event }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ bet_fixture.selection }}
                    </td>
                    <td
                      class="
                        px-6
                        py-4
                        whitespace-nowrap
                        text-sm text-gray-500
                        hidden
                        sm:table-cell
                      "
                    >
                      {{ bet_fixture.stake }}
                    </td>
                    <td
                      class="
                        px-6
                        py-4
                        whitespace-nowrap
                        text-sm text-gray-500
                        hidden
                        sm:table-cell
                      "
                    >
                      {{ bet_fixture.odds }}
                    </td>
                    <td
                      class="px-6 py-3 whitespace-nowrap text-sm text-gray-500"
                      @click.prevent="showDropdown(betKey, betFixtureKey)"
                    >
                      <span>
                        <span
                          v-if="
                            activeKeyBet != betKey ||
                            activeKeyBetFixture != betFixtureKey
                          "
                        >
                          <span
                            v-if="bet_fixture.result"
                            class="flex justify-between items-center"
                          >
                            <span
                              class="
                                px-2
                                inline-flex
                                text-xs
                                leading-5
                                rounded-full
                              "
                              :class="[
                                statusColor(bet_fixture.status, 'label'),
                              ]"
                            >
                              {{ bet_fixture.result }}
                            </span>
                          </span>
                          <span
                            v-else
                            class="
                              px-2
                              inline-flex
                              text-xs
                              leading-5
                              rounded-full
                            "
                            :class="[statusColor(bet_fixture.status, 'label')]"
                          >
                            {{ bet_fixture.status }}
                          </span>
                        </span>
                      </span>
                      <select
                        @focusout="handleFocusOut"
                        class="
                          block
                          pl-3
                          py-0.5
                          text-base
                          border-gray-300
                          focus:outline-none
                          focus:ring-indigo-500
                          focus:border-indigo-500
                          sm:text-sm
                          rounded-md
                        "
                        :class="{
                          'w-full sm:w-24 sm:-mr-24':
                            activeKeyBet == betKey &&
                            activeKeyBetFixture == betFixtureKey,
                        }"
                        @change.prevent="selectStatus(bet_fixture.status)"
                        v-show="
                          activeKeyBet == betKey &&
                          activeKeyBetFixture == betFixtureKey
                        "
                        v-model="bet_fixture.status"
                        :ref="'bet' + betKey + betFixtureKey"
                      >
                        <option
                          class="capitalize"
                          v-for="(status, key) in this.statuses"
                          :key="key"
                          :value="key"
                        >
                          {{ key }}
                        </option>
                      </select>
                    </td>
                    <td></td>
                  </tr>
                </template>
              </template>
            </tbody>
          </table>
          <div
            v-if="bets.data.length == 0"
            class="
              bg-white
              col-span-1
              px-6
              py-4
              whitespace-nowrap
              text-sm
              font-medium
              text-gray-900 text-center
            "
          >
            No results
          </div>
        </div>
      </div>
    </div>
    <pagination :data="bets" />
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated.vue";
import { Inertia } from "@inertiajs/inertia";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Pagination from "@/PageComponents/Pagination.vue";
import ActiveFilters from "@/PageComponents/ActiveFilters.vue";
import ShowFilterButton from "@/Components/ShowFilterButton.vue";
import moment from "moment";
import Button from "@/Components/Button.vue";
import { ChevronUpIcon, ChevronDownIcon } from "@heroicons/vue/outline";
import SportIcon from '@/Components/SportIcon.vue';

export default {
  layout: Layout,
  components: {
    TextInput,
    TextInputWithAddOn,
    Pagination,
    ActiveFilters,
    ShowFilterButton,
    Button,
    ChevronUpIcon,
    ChevronDownIcon,
    SportIcon
  },

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
          border: "",
          label: "bg-yellow-100 text-yellow-800",
        },
        won: {
          border: "border-green-500",
          label: "bg-green-100 text-green-800",
        },
        lost: {
          border: "border-red-500",
          label: "bg-red-100 text-red-800",
        },
        void: {
          border: "border-gray-300",
          label: "bg-gray-100 text-gray-800",
        },
        halfwon: {
          border: "border-green-500",
          label: "bg-green-100 text-green-800",
        },
        halflost: {
          border: "border-red-500",
          label: "bg-red-100 text-red-800",
        },
      },
      localFilters: {},
      highlighted: null,
      openedBets: [],
    };
  },

  created() {
    this.moment = moment;
    this.emitter.on("event:scroll", (id) => {
      this.scrollAndHighlight(id);
    });
  },

  methods: {
    showDropdown(betKey, betFixtureKey = -1) {
      if (!this.$page.props.userInfo.myPage || this.isStats) {
        return;
      }
      this.activeKeyBet = betKey;
      this.activeKeyBetFixture = betFixtureKey;
      var extraKey = "";
      if (betFixtureKey != '-1') {
        extraKey = betFixtureKey;
      }
      this.$nextTick(() => this.$refs["bet" + betKey + extraKey].focus());
    },

    handleFocusOut() {
      this.activeKeyBet = -1;
      this.activeKeyBetFixture = -1;
    },

    openBetDetail(id) {
      if (!this.$page.props.userInfo.myPage || this.activeKeyBet != -1) {
        return;
      }

      Inertia.visit(this.route("bet.show", id), {
        method: "get",
        data: {
          backUrl: window.location.href,
        },
      });
    },

    statusColor(status, type) {
      return this.statuses[status][type];
    },

    selectStatus(selectedStatus) {
      var currentBet = this.bets.data[this.activeKeyBet];
      var status = "";
      if (this.activeKeyBetFixture == -1) {
        if (selectedStatus == "won") {
          status = "+" + (currentBet.stake * currentBet.odds).toFixed(2);
        } else if (selectedStatus == "lost") {
          status = -currentBet.stake;
        } else if (selectedStatus == "new") {
          status = null;
        } else if (selectedStatus == "void") {
          status = 0;
        } else if (selectedStatus == "halfwon") {
          status =
            "+" + (currentBet.stake / 2 + currentBet.odds / 2).toFixed(2);
        } else if (selectedStatus == "halflost") {
          status = "-" + (currentBet.stake / 2).toFixed(2);
        }

        //currentBet?
        currentBet.result = status;
        currentBet.status = selectedStatus;
      } else {
        currentBet = currentBet.bet_fixture[this.activeKeyBetFixture];
        currentBet.status = selectedStatus;
      }

      Inertia.put(this.route("bet.updateStatus"), currentBet, {
        preserveScroll: true,
      });

      setTimeout(() => {
        this.activeKeyBet = -1;
        this.activeKeyBetFixture = -1;
      }, 1);
    },

    findPage(id) {
      this.$http.get(this.route("bet.pageNumber", id)).then((response) => {
        if (response.data) {
          Inertia.reload({
            data: {
              page: response.data,
            },
            onSuccess: () => {
              const el = this.$refs["bet-" + id];
              if (el) {
                this.scroll(id, el);
              }
            },
          });
        }
      });
    },

    scroll(id, el) {
      this.highlighted = id;
      setTimeout(() => {
        this.highlighted = null;
      }, 3000);
      el[0].scrollIntoView({ behavior: "smooth" });
    },

    scrollAndHighlight(id) {
      const el = this.$refs["bet-" + id];
      if (!el || el.length == 0) {
        this.findPage(id);
      }else{
        this.scroll(id, el);
      }
    },

    toggleAllBets(id) {
      const index = this.openedBets.indexOf(id);
      if (index > -1) {
        this.openedBets.splice(index, 1);
      } else {
        this.openedBets.push(id);
      }
    },
  },
};
</script>
