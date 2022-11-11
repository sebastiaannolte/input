<template>
  <Head title="Import" />
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div
          class="shadow overflow-hidden border-b border-gray-200 rounded-md"
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
                  "
                >
                  Stake
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Edit</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, key) in parsedData"
                :key="key"
                :class="key % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
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
                  {{ eventsToString(item.data.games) }}
                </td>

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
                  {{ item.data.odds }}
                </td>
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
                  {{ item.data.stake }}
                </td>
                <td
                  class="
                    px-6
                    py-4
                    whitespace-nowrap
                    text-right text-sm
                    font-medium
                  "
                >
                  <inertia-link
                    :href="toUrl(item)"
                    class="text-indigo-600 hover:text-indigo-900"
                    >Import</inertia-link
                  >
                  <button @click="destroy(item.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";

export default {
  layout: [Layout],

  props: {
    data: Object,
  },
  data() {
    return {
      parsedData: null,
    };
  },

  created() {
    this.parsedData = this.data.map(function (item) {
      return { id: item.id, data: JSON.parse(item.data) };
    });
  },

  methods: {
    destroy(id) {
      if (confirm("Are you sure you want to delete this import?")) {
        this.$inertia.delete(this.route("import.delete", id));
      }
    },
    eventsToString(games) {
      return games
        .map(function (test) {
          return test.event;
        })
        .join(", ");
    },

    toUrl(item) {
      item.data.importId = item.id;
      var query = this.toQueryString(item.data, "import");
      return `/sebastiaan?${query}`;
    },

    toQueryString(obj, prefix) {
      var str = [],
        k,
        v;
      for (var p in obj) {
        if (!obj.hasOwnProperty(p)) {
          continue;
        } // skip things from the prototype
        if (~p.indexOf("[")) {
          k = prefix
            ? prefix +
              "[" +
              p.substring(0, p.indexOf("[")) +
              "]" +
              p.substring(p.indexOf("["))
            : p;
          // only put whatever is before the bracket into new brackets; append the rest
        } else {
          k = prefix ? prefix + "[" + p + "]" : p;
        }
        v = obj[p];
        str.push(
          typeof v == "object"
            ? this.toQueryString(v, k)
            : encodeURIComponent(k) + "=" + encodeURIComponent(v)
        );
      }
      return str.join("&");
    },
  },
};
</script>