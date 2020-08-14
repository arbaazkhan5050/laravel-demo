<template>
  <div class="main-content">
    <div class="main-content--wrapper">
      <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>
      <title-bar title="New Inventory Item">
        <template slot="button">
          <button class="btn btn--primary" @click="saveItem">Save Item</button>
        </template>
      </title-bar>
      <div>
       <div class="form-group">
       		<div class="form-group">
            <label for>Name</label>
            <input type="text" v-model="item.name" />
            <span class="form-error" v-if="$v.item.name.$error">Name is required</span>
          </div>
          <div class="form-group">
            <label for>Email</label>
            <input type="email" v-model="item.email" />
            <span class="form-error" v-if="$v.item.email.$error">Email is required</span>
          </div>
          <div class="form-group">
            <label for>Phone Number</label>
            <input type="text" v-model="item.phone" />
            <span class="form-error" v-if="$v.item.phone.$error">Phone Number is required</span>
          </div>
          <div class="form-group">
            <label for>Address</label>
            <input type="text" v-model="item.address" />
            <span class="form-error" v-if="$v.item.address.$error">Address is required</span>
          </div>
          <div class="form-group">
            <label for>Zip Code</label>
            <input type="number" v-model="item.zip" />
            <span class="form-error" v-if="$v.item.zip.$error">Zip Code is required</span>
          </div>
        </div>
        <div class="row split-1-1">
          <image-uploader></image-uploader>
        </div>
      </div>
    </div>
    <confirm-delete  v-if="showModal" :id="id" :hideBtn="hideBtn" :modalTitle="modalTitle" :errorModalTitle="errorModalTitle"></confirm-delete>
  </div>
</template>

<script>
import TitleBar from "./../shared/TitleBar.vue";
import ImageUploader from "./../shared/ImageUploader.vue";
import { api } from "../../config";
import { upload } from "../../file-upload.fake.service";
import { Event } from "../../app.js";
import { required } from "vuelidate/lib/validators";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import ConfirmDelete from "./../Form/DeleteConfirmationModal.vue";
export default {
  components: {
    "title-bar": TitleBar,
    "image-uploader": ImageUploader,
    "confirm-delete": ConfirmDelete,
    Loading
  },
  data() {
    return {
      showModal : "",
      id : "",
      data : "",
      modalTitle : "",
      hideBtn : "",
      errorModalTitle : "",
      item: {
        name: "",
        email: "",
        phone: "",
        address: "",
        zip: "",
      },
      error: {
        images: "",
        selectedImage: ""
      },
      isDisabled: true,
      isLoading: false,
      fullPage: true,
    };
  },
  validations: {
    item: {
      name: { required },
      email : { required },
      phone: { required },
      address: { required },
      zip: { required },
    }
  },
  methods: {
    saveItem() {
      
      this.$v.item.$touch();
      if (this.$v.item.$error) {
        return;
      }

      if (this.item.selectedImage == ""  && this.item.images.length > 0) {
        this.error.selectedImage = "One featured image must be selected.";
        Event.$emit("empty-image", { error: this.error.selectedImage });
        return;
      } else {
        this.error.selectedImage = "";
      }

      let form_data = new FormData();
      if (this.item.images) {
        for (var i = 0; i < this.item.images.length; i++) {
          let file = this.item.images[i];
          form_data.append("images[" + i + "]", file);
        }
      }
   
      form_data.append("name", this.item.name);
      form_data.append("email", this.item.email);
      form_data.append("Phone", this.item.Phone);
      form_data.append("address", this.item.address);
      form_data.append("zip", this.item.zip);
      form_data.append("primaryImage", this.item.selectedImage);
      form_data.append("primaryImageType", this.item.selectedImage);

      this.isLoading = true;
      axios.post(api.storeProduct, form_data).then(res => {
        if (res.data["success"] == true) {
          this.$noty.success("Product has been added!", {
            closeWith: ["click", "button"]
          });
          this.isLoading = false;
          this.$router.push({ name: "index" });
        } else {
          this.$noty.error(
            "There was an issue adding this product. Please try again..",
            {
              closeWith: ["click", "button"]
            }
          );
        }
      });
    },
    setImage(images) {
      this.item.images.push(images.images);
    },
    resetImages() {
      this.item.images = [];
    },
    hideModal() {
       this.showModal= false;
    }
  },
  created() {
    Event.$on("image-upload", images => {
      this.setImage(images);
    });
    Event.$on("reset-images", this.resetImages);
    Event.$on("featured-image", name => {
      this.item.selectedImage = name.name;
      this.item.selectedImageId = name.id;
    });
    Event.$on("image-delete", id => {
      if (id.id == this.item.selectedImageId) {
        this.item.selectedImage = ""
      }
      this.item.images.splice(id.id , 1);
    });

    Event.$on("open-delete-modal", data => {
       this.showModal= true;
       this.id = data.id;
       this.data = data;
       this.errorModalTitle = '';
       this.modalTitle = "Are you sure you want to delete this " + data.label + ': ' +  data.name + ' ?';
       this.hideBtn = true;
    });

    Event.$on("confirm-modal", status => {
        this.deleteData(this.data);
    });

    Event.$on("cancel-modal", action => {
        this.hideModal();
    });

  },
  beforeDestroy(){
     Event.$off('confirm-modal')
  }
};
</script>

<style lang="scss" scoped>
input[type="number"] {
  width: 100%;
  font-size: 1em;
  padding: 15px 15px;
  border-radius: 10px;
  background: white;
  border: 1px solid #e5e5e5;
  box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.08);
}
.form-field input {
  margin-bottom: 12px;
}
.form-error {
  font-size: 0.75rem;
  font-weight: 700;
  color: #cc4b37;
  display: inherit;
  padding-left: 6px;
}
.super-select-error {
  margin-top: 12px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #cc4b37;
  display: inherit;
  padding-left: 6px;
}
.fadeIn-enter-active,
.fadeIn-leave-active {
  transition: opacity 0.15s;
}
.fadeIn-enter, .fadeIn-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
.error-color {
  color: red;
}
</style>
