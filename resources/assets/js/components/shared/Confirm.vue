<template>
  <div class="modal-overlay" v-if="showModal">
    <div class="modal-content">
      <div class="modal-content--header">
        <h4 class="modal-content--title">
          Are you sure you want to delete this item?
        </h4>
      </div>
      <div class="modal-content--main">
        <div class="button-group">
          <button class="btn" @click="closeModal">No, Cancel</button>
          <button class="btn btn--warning" @click="deleteProduct()">
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Event } from "../../app.js";
import { api } from "../../config";

export default {
  data() {
    return {
      showModal: false,
    };
  },
  props: {
      id: {
        type: Number,
        required: true
      }
    },
  methods: {
    closeModal() {
      Event.$emit("cancel-modal");
    },
    clearForm() {
      this.id = null;
    },
    deleteProduct(){
       axios.delete(api.deleteItem + this.id).then(res => {
       this.$noty.success('Item Deleted Successfully', {
              closeWith: ['click', 'button']
            });
       Event.$emit("item-deleted");
      });
    }
  },
  mounted() {
    this.showModal= true ;
  }
};
</script>

<style lang="scss" scoped>
.modal-content {
  background: #ffefef;
  border: 1px solid #e4b4b4;
  padding: 25px;
  border-radius: 15px;
  &--header {
    // border-bottom: 1px solid #ead078;
    margin-bottom: 25px;
    h4 {
      font-size: 1.3rem;
      letter-spacing: 0.5px;
      padding-bottom: 15px;
      text-transform: none;
      width: 70%;
      font-family: "Nunito Sans";
      font-weight: 700;
      line-height: 1.35;
      text-align: center;
      margin: 0 auto;
    }
    p {
      font-size: 1rem;
      line-height: 1.3;
      width: 70%;
      margin: 0 auto;
      text-align: center;
    }
  }
  &--main {
    .form-field + .form-field {
      margin-top: 20px;
    }
    .button-group {
      margin-top: 35px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .btn--warning {
      background-color: #d57b7b;
      border-radius: 15px;
      color: #730808;
      padding: 15px 30px;
    }
  }
}
</style>
