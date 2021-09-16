<template>
  <active-filters :prop-filters="filters" :filter-route="filterRoute" />
  <div class="flex flex-col">
    <div class="-my-2">
      <div class="py-2 align-middle inline-block min-w-full">
        <div
          class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
          <table class="table-fixed min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="
                    hidden
                    sm:table-cell
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
                    w-full
                    sm:w-auto
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
                    w-full
                    sm:w-auto
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
                    hidden
                    sm:table-cell
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
                    hidden
                    sm:table-cell
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
                v-for="(bet, key) in bets.data"
                :ref="'bet-' + bet.id"
                :key="key"
                :class="[
                  key % 2 === 0 ? 'bg-white ' : 'bg-gray-50 ',
                  highlighted == bet.id ? ' bg-indigo-200' : '',
                  statusColor(bet.status, 'border'),
                ]"
                @click="openBetDetail(bet.id)"
                class="cursor-pointer border-r-8"
              >
                <td
                  class="
                    hidden
                    sm:table-cell
                    px-6
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
                  class="px-6 py-4 sm:whitespace-nowrap text-sm text-gray-500"
                >
                  {{ bet.event }}
                </td>
                <td
                  class="px-6 py-4 sm:whitespace-nowrap text-sm text-gray-500"
                >
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
                  @click.prevent="showDropdown(key)"
                >
                  <span>
                    <span v-if="activeKey != key">
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
                            font-semibold
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
                          font-semibold
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
                    @change.prevent="selectStatus(bet.status)"
                    v-show="activeKey == key"
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
import Layout from "@/Layouts/Authenticated";
import { Inertia } from "@inertiajs/inertia";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Pagination from "@/PageComponents/Pagination";
import FiltersSlideOver from "@/PageComponents/FiltersSlideOver";
import ActiveFilters from "@/PageComponents/ActiveFilters";
import ShowFilterButton from "@/Components/ShowFilterButton";
import moment from "moment";

export default {
  layout: Layout,
  components: {
    TextInput,
    TextInputWithAddOn,
    Pagination,
    FiltersSlideOver,
    ActiveFilters,
    ShowFilterButton,
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
  },

  data() {
    return {
      betData: this.bets.data,
      selectedStatus: false,
      activeKey: -1,
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
    };
  },

  created() {
    this.moment = moment;
    this.emitter.on("event:scroll", (id) => {
      this.scrollAndHighlight(id);
    });
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

    openBetDetail(id) {
      if (!this.$page.props.userInfo.myPage || this.activeKey != -1) {
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
      var currentBet = this.bets.data[this.activeKey];

      var status = false;

      if (selectedStatus == "won") {
        status = "+" + (currentBet.stake * currentBet.odds).toFixed(2);
      } else if (selectedStatus == "lost") {
        status = -currentBet.stake;
      } else if (selectedStatus == "new") {
        status = null;
      } else if (selectedStatus == "void") {
        status = 0;
      } else if (selectedStatus == "halfwon") {
        status = "+" + (currentBet.stake / 2 + currentBet.odds / 2).toFixed(2);
      } else if (selectedStatus == "halflost") {
        status = "-" + (currentBet.stake / 2).toFixed(2);
      }

      this.bets.data[this.activeKey].result = status;
      this.bets.data[this.activeKey].status = selectedStatus;

      Inertia.put(
        this.route("bet.updateStatus"),
        this.bets.data[this.activeKey],
        {
          preserveScroll: true,
        }
      );

      setTimeout(() => {
        this.activeKey = -1;
      }, 1);
    },

    scrollAndHighlight(id) {
      const el = this.$refs["bet-" + id];

      if (el) {
        this.highlighted = id;
        setTimeout(() => {
          this.highlighted = null;
        }, 3000);
        el.scrollIntoView({ behavior: "smooth" });
      }
    },
  },

  computed: {
    user() {
      return this.$page.props.auth.user;
    },
  },
};
</script>
