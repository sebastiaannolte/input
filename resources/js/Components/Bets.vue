<template>
  <div
    class="
      px-4
      w-full
      font-light
      leading-4
      text-gray-800
      cursor-default
      flex
      justify-end
    "
  >
    <p
      align="right"
      class="p-0 mx-0 mt-0 mb-5 text-base text-gray-800 box-border"
    >
      <button
        type="button"
        @click.prevent="showFilter"
        id="filterswitch"
        class="
          m-0
          text-red-500
          no-underline
          box-border
          hover:text-red-500
          focus:text-red-500
        "
      >
        <template v-if="!filterStatus">Show filter</template>
        <template v-else>Hide filter</template>
      </button>
      >
    </p>
  </div>
  <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9 mb-5" v-if="filterStatus">
    <section aria-labelledby="payment-details-heading">
      <form action="#" method="POST">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <div>
              <h2
                id="payment-details-heading"
                class="text-xl leading-6 font-medium text-gray-900 font-bold"
              >
                Filters
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                Update your billing information. Please note that updating your
                location could affect your tax rates.
              </p>
            </div>
            <div class="mt-6 grid grid-cols-6 gap-4">
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.from.value"
                  :error="errors"
                  type="date"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="From"
                />
              </div>

              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.minOdds.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Min odds"
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input-with-add-on
                  v-model="filters.minStake.value"
                  :error="errors"
                  add-on="units"
                  label="Min stake"
                  type="text"
                  id="stake"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.maxStake.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Sport"
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.bookie.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Bookie"
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <label
                  class="
                    block
                    text-sm
                    font-medium
                    text-gray-700
                    dark:text-gray-400
                  "
                  >Status:</label
                >
                <select
                  class="
                    pl-3
                    pr-10
                    py-2
                    block
                    pl-3
                    py-2
                    text-base
                    border-gray-300
                    focus:outline-none
                    focus:ring-indigo-500
                    focus:border-indigo-500
                    sm:text-sm
                    rounded-md
                    w-full
                  "
                  v-model="filters.status.value"
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
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.to.value"
                  :error="errors"
                  type="date"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="To"
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.maxOdds.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Max odds"
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input-with-add-on
                  v-model="filters.maxStake.value"
                  :error="errors"
                  add-on="units"
                  label="Max stake"
                  type="text"
                  id="stake"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                />
              </div>
              <div class="col-span-4 sm:col-span-1">
                <text-input
                  v-model="filters.tipster.value"
                  :error="errors"
                  label="Tipster"
                  type="text"
                  id="tipster"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                />
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <button
              @click.prevent="resetFilters"
              class="
                mr-2
                bg-gray-800
                border border-transparent
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-white
                hover:bg-gray-900
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
              "
            >
              Reset
            </button>
            <button
              @click.prevent="filterResults"
              class="
                bg-gray-800
                border border-transparent
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-white
                hover:bg-gray-900
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
              "
            >
              Filter
            </button>
          </div>
        </div>
      </form>
    </section>
  </div>
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div
          class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
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
                  Time
                </th>
                <th
                  scope="col"
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
                  scope="col"
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
                  Selection
                </th>
                <th
                  scope="col"
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
                  Stake
                </th>
                <th
                  scope="col"
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
                  Odds
                </th>
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                    w-40
                  "
                >
                  Result
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(bet, key) in filteredBets.slice(
                  (pageNumber - 1) * perPage,
                  pageNumber * perPage
                )"
                :key="key"
                :class="
                  key % 2 === 0
                    ? 'bg-white ' + statusColor(bet.status)
                    : 'bg-gray-50 ' + statusColor(bet.status)
                "
                @click="openBetDetail(bet.id)"
                class="cursor-pointer"
              >
                <td
                  class="
                    px-6
                    py-4
                    whitespace-nowrap
                    text-sm
                    font-medium
                    text-gray-900
                  "
                >
                  {{ bet.date }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ bet.event }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ bet.selection }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ bet.stake }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ bet.odds }}
                </td>
                <td
                  class="px-6 py-3 whitespace-nowrap text-sm text-gray-500"
                  @click.prevent="
                    showDropdown(key + perPage * (pageNumber - 1))
                  "
                >
                  <div>
                    <div v-if="activeKey != key + perPage * (pageNumber - 1)">
                      <div
                        v-if="bet.result"
                        class="flex justify-between items-center"
                      >
                        <div>
                          {{ bet.result }}
                        </div>
                      </div>
                      <div v-else>
                        {{ bet.status }}
                      </div>
                    </div>
                  </div>
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
                    @change.prevent="selectStatus(bet.status)"
                    v-show="activeKey == key + perPage * (pageNumber - 1)"
                    v-model="bet.status"
                    :ref="'bet' + key"
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
              </tr>
            </tbody>
          </table>
          <div
            v-if="filteredBets.length == 0"
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
    <pagination
      v-model="pageNumber"
      :item-count="filteredBets.length"
      :per-page="perPage"
      @update:modelValue="changePage"
    />
  </div>
</template>



<script>
import Layout from "@/Layouts/Layout";
import { Inertia } from "@inertiajs/inertia";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Pagination from "@/Components/Pagination";

export default {
  layout: Layout,
  components: { TextInput, TextInputWithAddOn, Pagination },

  props: {
    bets: Object,
    errors: Object,
  },
  data() {
    return {
      selectedStatus: false,
      activeKey: -1,
      statuses: {
        new: "",
        won: "border-r-8 border-green-500",
        lost: "border-r-8 border-red-500",
        void: "border-r-8 border-gray-200",
      },
      filterStatus: false,
      filteredBets: null,
      pageNumber: 1,
      perPage: 25,
      filters: {
        from: {
          value: null,
          type: "min",
          col: "date",
          specialType: "date",
        },
        to: {
          value: null,
          type: "max",
          col: "date",
          specialType: "date",
        },
        minOdds: {
          value: null,
          type: "min",
          col: "odds",
        },
        maxOdds: {
          value: null,
          type: "max",
          col: "odds",
        },
        minStake: {
          value: null,
          type: "min",
          col: "stake",
        },
        maxStake: {
          value: null,
          type: "max",
          col: "stake",
        },
        sport: {
          value: null,
          type: "match",
          col: "date",
        },
        bookie: {
          value: null,
          type: "match",
          col: "bookie",
        },
        tipster: {
          value: null,
          type: "match",
          col: "tipster",
        },
        status: {
          value: null,
          type: "match",
          col: "status",
        },
      },
    };
  },

  created() {
    this.filteredBets = this.bets.bets;
  },

  methods: {
    showDropdown(key) {
      if (!this.$page.props.userInfo.myPage) {
        return;
      }
      this.activeKey = key;
      this.$nextTick(() => this.$refs["bet" + key].focus());
    },

    handleFocusOut() {
      this.activeKey = -1;
    },
    showFilter() {
      this.filterStatus = !this.filterStatus;
    },

    changePage() {
      // console.log(this.pageNumber);
    },

    openBetDetail(id) {
      if (!this.$page.props.userInfo.myPage || this.activeKey != -1) {
        return;
      }

      Inertia.visit(this.route("bet.show", id), { method: "get" });
    },

    resetFilters() {
      this.filteredBets = this.bets.bets;
      for (var key in this.filters) {
        this.filters[key].value = null;
      }
    },

    statusColor(status) {
      return this.statuses[status];
    },

    filterResults() {
      this.pageNumber = 1;
      this.filteredBets = this.bets.bets;
      for (var key in this.filters) {
        var object = this.filters[key];
        if (object.value) {
          this.filteredBets = this.filteredBets.filter(function (e) {
            if (object.type == "match") {
              return object.value == e[object.col];
            } else if (object.type == "min") {
              if (object.specialType == "date") {
                return object.value <= e[object.col].split(" ")[0];
              }
              return object.value <= e[object.col];
            } else if (object.type == "max") {
              if (object.specialType == "date") {
                return object.value >= e[object.col].split(" ")[0];
              }
              return object.value >= e[object.col];
            }
          });
        }
      }
    },

    selectStatus(selectedStatus) {
      var currentBet = this.bets.bets[this.activeKey];
      var status = false;

      if (selectedStatus == "won") {
        status = "+" + (currentBet.stake * currentBet.odds).toFixed(2);
      } else if (selectedStatus == "lost") {
        status = -currentBet.stake;
      } else if (selectedStatus == "new") {
        status = null;
      } else if (selectedStatus == "lost") {
        status = 0;
      }

      this.bets.bets[this.activeKey].result = status;
      this.bets.bets[this.activeKey].status = selectedStatus;
      this.filteredBets[this.activeKey] = this.bets.bets[this.activeKey];

      Inertia.put(
        this.route("bet.updateStatus"),
        this.bets.bets[this.activeKey],
        {
          preserveScroll: true,
        }
      );
      // If no timeout set, click will registered first
      setTimeout(() => {
        this.activeKey = -1;
      }, 1);
    },
  },
  watch: {
    // whenever question changes, this function will run
    bets: function (newQuestion, oldQuestion) {
      this.filteredBets = newQuestion.bets;
    },
  },

  computed: {
    user() {
      return this.$page.props.auth.user;
    },
  },
};
</script>
