<template>
  <div class="modal-overlay" v-if="showModal">
    <div class="modal-content">
      <button @click="closeModal" type="button" class="close cancel-button" data-dismiss="modal">&times;</button>
      <div class="modal-content--header">
        <h4 v-if="errorModalTitle" class="modal-content--title">
            <span class="error"> Error: </span> {{ errorModalTitle }}
        </h4>
        <h4 class="modal-content--title">
          {{ modalTitle }}
        </h4>
      </div>
      <div class="modal-content--main">
        <div class="button-group">
          <button v-if="hideBtn" class="btn" @click="closeModal">No, Cancel</button>
          <button v-if="hideBtn" class="btn btn--warning" @click="deleteProduct()">
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
  props:['id', 'modalTitle' , 'hideBtn', 'errorModalTitle'],
  data() {
    return {
      showModal: false,
    };
  },
  methods: {
    closeModal() {
      Event.$emit("cancel-modal");
    },
    clearForm() {
      this.id = null;
    },
    deleteProduct(){
       Event.$emit("confirm-modal",true);
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
.error{
  color : red;
}
.cancel-button{
    border: unset;
    text-align: center;
    display: block;
    background: unset;
    font-size: 43px;
    font-weight: 700;
    color: #ffffff;
    filter: alpha(opacity=20);
    display: block;
    margin-top: -79px;
    margin-right: -60px;
    display: block;
    float: right;

}
</style>
