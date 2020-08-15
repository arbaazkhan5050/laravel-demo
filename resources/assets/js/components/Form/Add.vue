<template>
  <div class="container">
    <div class="main-content--wrapper">
      <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>

     
      <title-bar title="New Inventory Item">
     
      </title-bar>
      <div>
              <button class="btn btn--primary" @click="saveItem">Save Item</button>
        	<div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" v-model="item.name" />
            <span class="form-error" v-if="$v.item.name.$error">Name is required</span>
          </div>
          <div class="form-group">
            <label for>Email</label>
            <input type="email" class="form-control" v-model="item.email" />
            <div v-if="$v.item.email.$error">
              <span class="form-error" v-if="!$v.item.email.required">Email is required</span>
              <span class="form-error" v-if="$v.item.email.required && !$v.item.email.email">Email is not a properly formatted email address</span>
            </div>
          </div>
          <div class="form-group">
            <label for>Phone Number</label>
            <input type="text" @input="acceptNumber" class="form-control" v-model="item.phone" />
            <div v-if="$v.item.phone.$error">
              <span class="form-error" v-if="!$v.item.phone.required">Phone Number is required</span>
              <span class="form-error" v-if="$v.item.phone.required && !$v.item.phone.phoneValid">Phone Number is not valid.</span>
            </div>
          </div>
          <div class="form-group">
            <label for>Address</label>
            <input type="text" class="form-control" v-model="item.address" />
            <span class="form-error" v-if="$v.item.address.$error">Address is required</span>
          </div>
          <div class="form-group">
            <label for>Zip Code</label>
            <input type="text" class="form-control" v-model="item.zip" />
            <div v-if="$v.item.zip.$error">
              <span class="form-error" v-if="!$v.item.zip.required">Zip Code is required</span>
              <span class="form-error" v-if="!$v.item.zip.minLength">Zip Code should not be less than 5 char</span>
              <span class="form-error" v-if="$v.item.zip.minLength && !$v.item.zip.numeric">Zip Code must be numeric</span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <image-uploader></image-uploader>
        </div>
      </div>
  </div>
</template>

<script>
import TitleBar from "./../shared/TitleBar.vue";
import ImageUploader from "./../shared/ImageUploader.vue";
import { api } from "../../config";
import { upload } from "../../file-upload.fake.service";
import { Event } from "../../app.js";
import { required, minLength, email , numeric  } from "vuelidate/lib/validators";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
const isPhone = (value) => /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/.test(value);  //phone valid
export default {
  components: {
    "title-bar": TitleBar,
    "image-uploader": ImageUploader,
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
        images: [],
        selectedImage: "",
        selectedImageType: "",
        selectedImageId: ""
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
      email : { 
        required,
        email
      },
      phone: { 
        required,
        phoneValid:isPhone
       },
      address: { required },
      zip: { 
        required,
        minLength: minLength(5),
        numeric
      },
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
      form_data.append("phone", this.item.phone);
      form_data.append("address", this.item.address);
      form_data.append("zip", this.item.zip);
      form_data.append("primaryImage", this.item.selectedImage);
      form_data.append("primaryImageType", this.item.selectedImage);

      this.isLoading = true;
      axios.post(api.storeItem, form_data).then(res => {
        if (res.data["success"] == true) {
          this.$noty.success("Item has been added!", {
            closeWith: ["click", "button"]
          });
          this.isLoading = false;
          this.$router.push({ name: "form" });
        } else {
          this.$noty.error(
            "There was an issue adding this Item. Please try again..",
            {
              closeWith: ["click", "button"]
            }
          );
        }
      });
    },
    acceptNumber() {
        var x = this.item.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/); 
        this.item.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    },
    setImage(images) {
      this.item.images.push(images.images);
    },
    resetImages() {
      this.item.images = [];
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
  }
};
</script>

<style lang="scss" scoped>

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

.error-color {
  color: red;
}
.btn--primary{
  display: block;
  float: right;
  margin-bottom: 10px;
}
</style>
