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
          this.$toast.success(this.$page.props.flash.success, {
            position: "bottom-left",
          });
        }

        if (
          (this.$page.props.flash.error ||
            Object.keys(this.$page.props.errors).length > 0) &&
          this.show
        ) {
          var errorMessage = "";
          if (this.$page.props.flash.error) {
            errorMessage = this.$page.props.flash.error;
          } else {
            if (Object.keys(this.$page.props.errors).length === 1) {
              errorMessage = "There is one form error";
            } else {
              errorMessage =
                "There are " +
                Object.keys(this.$page.props.errors).length +
                " form errors";
            }
          }

          this.$toast.error(errorMessage, { position: "bottom-left" });
        }
      },
      deep: true,
    },
  },
};
</script>