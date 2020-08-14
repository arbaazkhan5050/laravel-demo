<template>
  <div class="main-content">
    <div class="main-content--wrapper">
      <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>
      <title-bar title="Update Inventory Item">
        <template slot="button">
          <button class="btn btn--primary" @click="updateItem">
            Update Item
          </button>
        </template>
      </title-bar>
      <div v-show="!isLoading">
        <div class="row">
          <div class="form-field">
            <label for>Name</label>
            <input type="text" v-model="item.name" />
            <span class="form-error" v-if="$v.item.name.$error"
              >Name is required</span
            >
          </div>
          <div class="form-field">
            <super-select
              label="Category"
              v-if="selCat"
              :isDelete="'delete'"
              @inputData="updateOptions"
              :options="categories"
              @input="selOption($event)"
              @blur="blur"
              @selectedOption="selectedOptions"
              v-model="item.category"
              :placeHolder="item.category"
            ></super-select>
            <span class="super-select-error" v-if="$v.item.category.$error" >Choose a Category</span>
          </div>
          <div class="form-field">
            <super-select
              label="Manufacturer"
              v-if="selManufacturer"
              @inputData="updateOptions"
              :isDelete="'delete'"
              :options="brands"
              v-model="item.brand"
              :placeHolder="item.brand"
            ></super-select>
            <span class="super-select-error" v-if="$v.item.brand.$error"
              >Choose a Brand</span
            >
          </div>
          <div class="form-field">
            <label for>Year</label>
            <input
              type="number"
              onkeypress="if(this.value.length==4) return false;"
              v-model="item.year"
            />
            <span class="form-error" v-if="$v.item.year.$error"
              >Year is required</span
            >
          </div>
          <div class="form-field">
            <label for>Stock Number</label>
            <input type="text" v-model="item.stock_number" />
            <span class="form-error" v-if="$v.item.stock_number.$error"
              >Stock# is required</span
            >
          </div>
          <div class="form-field">
            <label for>Serial Number</label>
            <input type="text" v-model="item.serial_number" />
            <span class="form-error" v-if="$v.item.serial_number.$error"
              >Serial# is required</span
            >
          </div>
          <div class="form-field">
            <super-select
              label="Model"
              v-if="selModel"
              :isDelete="'delete'"
              :options="models"
              @inputData="updateOptions"
              v-model="item.modelName"
              :placeHolder="item.modelName"
            ></super-select>
            <span class="form-error" v-if="$v.item.modelName.$error"
              >Choose a Model</span
            >
          </div>
          <div class="form-field">
            <label for>Quantity</label>
            <input type="number" v-model="item.quantity" />
            <span class="form-error" v-if="$v.item.quantity.$error"
              >Quantity is required</span
            >
          </div>
          <div class="row split-3-1">
            <div class="form-field">
              <label for>Condition</label>
              <div class="toggler">
                <div
                  class="option"
                  @click="item.condition = 'new'"
                  :class="{ selected: item.condition == 'new' }"
                >
                  New
                </div>
                <div
                  class="option"
                  @click="item.condition = 'used'"
                  :class="{ selected: item.condition == 'used' }"
                >
                  Used
                </div>
              </div>
            </div>
            <transition name="fadeIn">
              <div class="form-field" v-show="item.condition == 'used'">
                <label for>Hours</label>
                <input
                  type="text"
                  v-model="item.hours"
                  style="min-width:100px;"
                />
              </div>
            </transition>
          </div>
          <div class="form-field">
            <label for>Price</label>
            <input
              type="number"
              v-model="item.price"
              v-on:blur="formatPriceIntoCurrency(item.price)"
            />
            <span class="form-error" v-if="$v.item.price.$error"
              >Price is required</span
            >
          </div>
          <div class="form-field">
            <super-select
              v-if="item.category"
              label="Location"
              :isFilter="'filter'"
              :options="locations"
              v-model="item.location"
              :placeHolder="item.location"
            ></super-select>
          </div> 
        </div>
        <div class="row full-width">
          <div class="form-field">
            <label for>Description</label>
            <textarea cols="30" rows="10" v-model="item.description"></textarea>
          </div>
        </div>
        <div class="row split-1-1">
          <image-uploader></image-uploader>
          <file-uploader></file-uploader>
        </div>
        <div class="row full-width time-stamp">
          <p class="time">This item was added on {{ item.created_at }}.</p>
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
      selCat : "",
      selManufacturer : "",
      selModel : "",
      item: {
        id: "",
        name: "",
        category: "",
        category_id: "",
        brand_id: "",
        brand: "",
        year: "",
        modelName: "",
        model_id: "",
        serial_number: "",
        stock_number: "",
        condition: "new",
        hours: "",
        price: "",
        description: "",
        quantity: "",
        images: [],
        documents: [],
        selectedImage: "",
        selectedImageType: "old",
        selectedImageId: "",
        created_at: "",
        location : ""
      },
      error: {
        selectedImage: ""
      },
      categories: [],
      brands: [],
      models: [],
      locations : [
      {
        'id' : 1,
        'name' : 'Abilene, TX',
      },
      {
        'id' : 2,
        'name' : 'Amarillo, TX',
      },
      {
        'id' : 3,
        'name' : 'Beaumont, TX',
      },
      {
        'id' : 4,
        'name' : 'Belton, TX',
      },
      {
        'id' : 5,
        'name' : 'Brenham, TX',
      },
      {
        'id' : 6,
        'name' : 'Clovis, NM',
      },
      {
        'id' : 8,
        'name' : 'Euless, TX',
      },
      {
        'id' : 9,
        'name' : 'Houston, TX',
      },
      {
        'id' : 10,
        'name' : 'Manor / Austin, TX',
      },
      {
        'id' : 11,
        'name' : 'Midland, TX',
      },
      {
        'id' : 11,
        'name' : 'Odessa, TX',
      },
      {
        'id' : 12,
        'name' : 'Perryton, TX',
      },
      {
        'id' : 13,
        'name' : 'San Angelo, TX',
      },
      {
        'id' : 14,
        'name' : 'San Antonio, TX',
      },
      {
        'id' : 15,
        'name' : 'Sherman, TX',
      },
      {
        'id' : 16,
        'name' : 'Terrell, TX',
      },
      {
        'id' : 17,
        'name' : 'Tyler, TX',
      },
      {
        'id' : 18,
        'name' : 'Wichita Falls, TX',
      }
      ],
      isDisabled: false,
      productId: "",
      isLoading: false,
      fullPage: true
    };
  },
  validations: {
    item: {
      name: { required },
      category: { required },
      brand: { required },
      year: { required },
      modelName: { required },
      serial_number: { required },
      stock_number: { required },
      price: { required },
      quantity: { required }
    }
  },
  methods: {
     hideModal() {
       this.showModal= false;
    },
    deleteData(Arr){
      this.isLoading = true;
      let url = '';
      let label = Arr.label;
      switch (label) {
        case "Manufacturer":
          url = api.storeManufacturers + "/" + Arr.id;
          break;
        case "Category":
             url = api.storeCategory + "/" + Arr.id;
          break;
        case "Model":
           url = api.storeModel + "/" + Arr.id;
           break;
      }
      axios.delete(url).then(res => {
        if(res.data.result.error){
            this.isLoading = false;
            this.modalTitle = '';
            this.hideBtn = false;
            this.errorModalTitle = res.data.result.error;
        } else {
            this.isLoading = false;
            this.$noty.success("Record deleted Successfully!");
            this.hideModal();  
            this.getDropMenu(label);
        }
    });
    },
    getDropMenu(title){

    switch (title) {
        case "Manufacturer":
          this.getBrand();
          break;
        case "Category":
          this.getCategory();
          break;
        case "Model":
          this.getModel();
          break;
      }
    },
    selectedOptions(data) {
      if (data.label === "Category") {
        if (data.val) {
          this.isDisabled = false;
        }
      }
    },
    blur(data) {
      if (data.label === "Category") {
        if (data.val === null) {
          this.isDisabled = true;
        }
      }
    },
    selOption() {},
    updateOptions(Arr) {
      let length = Arr.options.length - 1;
      let options = { id: length, name: Arr.options[length].name };
      let urls = "";
      switch (Arr.label) {
        case "Manufacturer":
          axios.post(api.storeManufacturers, options).then(res => {
              this.getBrand();
            this.item.brand_id = res.data.id;
          });
          break;
        case "Category":
          axios.post(api.storeCategory, options).then(res => {
              this.getCategory();
          });
          this.isDisabled = false;
          break;
        case "Model":
          axios.post(api.storeModel, options).then(res => {
              this.getModel();
          });
          break;
      }
    },
    getProduct() {
      this.isLoading = true;
      setTimeout(() => {
        this.isLoading = false;
      }, 3500);

      axios.get(api.getProduct + this.productId).then(res => {
        this.item.id = res.data.id;
        this.item.name = res.data.name;
        this.item.description = res.data.description;
        this.item.serial_number = res.data.serial_number;
        this.item.stock_number = res.data.stock_number;
        this.item.year = res.data.year;
        this.item.quantity = res.data.quantity;
        this.item.category_id = res.data.category_id;
        this.item.brand_id = res.data.brand_id;
        this.item.model_id = res.data.model_id;
        this.item.price = res.data.price;
        this.item.condition = res.data.condition;
        this.item.location = res.data.location;
        
        if (res.data.hours != null) {
          this.item.hours = res.data.hours;
        }




      this.selCat = res.data.categories.name;
      this.selManufacturer = res.data.brand.name;
      this.selModel = res.data.brand.name;



        this.item.category = res.data.categories.name;
        this.item.brand = res.data.brand.name;
        this.item.modelName = res.data.model.name;
        this.item.created_at = res.data.created_at;

        for (let key in res.data.images) {
          this.item.images.push(res.data.images[key]);
          if (res.data.images[key].primary_image) {
            this.item.selectedImage = res.data.images[key].url;
          }
        }
        Event.$emit("edit-images", { images: this.item.images });
        this.item.images = [];
        for (let key in res.data.documents) {
          this.item.documents.push(res.data.documents[key]);
        }
        Event.$emit("edit-documents", { images: this.item.documents });
        this.item.documents = [];
      });
    },
    formatPriceIntoCurrency: function(id) {
      this.item.price = parseInt(id).toFixed(2);
    },
    updateItem() {
      this.$v.item.$touch();
      if (this.$v.item.$error) {
        return;
      }

      if (this.item.selectedImage == "" && this.item.images.length>0) {
        this.error.selectedImage = "One featured image must be selected.";
        Event.$emit("empty-image", { error: this.error.selectedImage });
        return;
      } else {
        this.error.selectedImage = "";
      }

      this.item.category_id = this.getId(this.categories, this.item.category);

      this.item.brand_id = this.getId(this.brands, this.item.brand);
      
      this.item.model_id = this.getId(this.models, this.item.modelName);

      let form_data = new FormData();
      if (this.item.images) {
        for (var i = 0; i < this.item.images.length; i++) {
          let file = this.item.images[i];
          form_data.append("images[" + i + "]", file);
        }
      }

      if (this.item.documents) {
        for (var i = 0; i < this.item.documents.length; i++) {
          let file = this.item.documents[i];
          form_data.append("documents[" + i + "]", file);
        }
      }

      form_data.append("id", this.productId);
      form_data.append("name", this.item.name);
      form_data.append("description", this.item.description);
      form_data.append("quantity", this.item.quantity);
      form_data.append("price", this.item.price);
      form_data.append("condition", this.item.condition);
      form_data.append("serial_number", this.item.serial_number);
      form_data.append("stock_number", this.item.stock_number);
      form_data.append("model_id", this.item.model_id);
      form_data.append("category_id", this.item.category_id);
      form_data.append("brand_id", this.item.brand_id);
      form_data.append("year", this.item.year);
      form_data.append("hours", this.item.hours);
      form_data.append("location", this.item.location);
      form_data.append("primaryImage", this.item.selectedImage);
      form_data.append("primaryImageType", this.item.selectedImageType);

      axios.post(api.updateProduct + this.productId, form_data).then(res => {
        this.$noty.success("Product has been updated!", {
          closeWith: ["click", "button"]
        });
        this.$router.push({ name: "index" });
      });
    },

    getId(array, key) {
      var result = null;
      for (var i = 0; i < array.length; i++) {
        if (array[i].name === key) {
          result = array[i].id;
          return result;
        }
      }
    },
    getCategory() {
      axios.get(api.getCategory).then(res => {
        const results = [];
        for (let key in res.data) {
          results.push(res.data[key]);
        }
        this.categories = results;
      });
    },
    getModel() {
      axios.get(api.getModel).then(res => {
        const results = [];
        for (let key in res.data) {
          results.push(res.data[key]);
        }
        this.models = results;
      });
    },
    getBrand() {
      axios.get(api.getBrand).then(res => {
        const results = [];
        for (let key in res.data) {
          results.push(res.data[key]);
        }
        this.brands = results;
      });
    },
    setImage(images) {
      this.item.images.push(images.images);
    },
    setDocument(documents) {
      this.item.documents.push(documents.documents);
    },
    resetImages() {
      this.item.images = [];
    }
  },
  created() {
    this.productId = this.$route.params.id;
    this.getProduct();
    this.getCategory();
    this.getBrand();
    this.getModel();
  },
  mounted() {
    Event.$on("reset-images", this.resetImages);

    Event.$on("image-upload-edit", images => {
      this.setImage(images);
    });
    Event.$on("document-upload-edit", documents => {
      this.setDocument(documents);
    });
    Event.$on("featured-image", name => {
      this.item.selectedImage = name.name;
      this.item.selectedImageType = name.type;
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
       this.modalTitle = "are you sure you want to delete this " + data.label + ' : ' +  data.name + ' ?';
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
  padding-left: 6px;
}
.loader {
  transform: scale(1);
  min-height: 400px;
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
