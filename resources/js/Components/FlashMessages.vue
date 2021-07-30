<script>
export default {
  data() {
    return {
      show: true,
    };
  },

  watch: {
    "$page.props.flash": {
      handler() {
        this.show = true;

        if (this.$page.props.flash.success && this.show) {
          this.$toast.success(this.$page.props.flash.success);
        }

        if (
          (this.$page.props.flash.error ||
            Object.keys(this.$page.props.errors).length > 0) &&
          this.show
        ) {
          this.$toast.error(this.$page.props.flash.error);
        }
      },
      deep: true,
    },
  },
};
</script>