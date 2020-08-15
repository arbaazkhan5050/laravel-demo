<template>
  <div class="container">
    <div class="main-content--wrapper">
      <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>
      <router-link
            :to="{ name: 'form.Add' }"
            class="btn btn--primary add"
            >Add</router-link
          >
      <title-bar title="Form List" :has-filter="true">
        
      </title-bar>
      <div>
        <div class="inventory-table">
          <div class="table--header">
            <div class="table--row">
              <div class="table-item">
                <h4></h4>
              </div>
              <div class="table-item">
                <h4>Name</h4>
              </div>
              <div class="table-item">
                <h4>Email</h4>
              </div>
              <div class="table-item">
                <h4>Phone Number</h4>
              </div>
              <div class="table-item">
                <h4>Address</h4>
              </div>
              <div class="table-item">
                <h4>Zip Code</h4>
              </div>
              <div class="table-item">
                  <h4>Action</h4>
                </div>
            </div>
          </div>
          <div class="table--body" v-if="noItem == true">
            <div
              class="table--row"
              v-for="item in items"
              :key="item.id"
              @click="editItem(item.id)"
            >
              <div class="table-item product-tn">
                 <p data-title="Manufacturer"> <img
                    :src="siteUrl + path + item.image"
                    onError="this.onerror=null; this.src='https://dummyimage.com/300x360/fff/aaa';"
                  /> </p>
              </div>
              <div class="table-item">
                <p data-title="Manufacturer">{{ item.name }}</p>
              </div>
              <div class="table-item">
                <p data-title="Category">{{ item.email }}</p>
              </div>
              <div class="table-item">
                <p data-title="Model">{{ item.phone }}</p>
              </div>
              <div class="table-item">
                <p data-title="Year">{{ item.address }}</p>
              </div>
              <div class="table-item">
                <p data-title="Stock #">{{ item.zip }}</p>
              </div>
              <div class="table-item">
                <button class="btn btn--primary" @click.stop="confirmDelete(item.id)"> Delete</button>
              </div>
            </div>
          </div>
          <div class="table--body" v-else>
            <div class="no-product">
              <br />
              <br />
              <b>No product found</b>
            </div>
          </div>
        </div>
      </div>
      <confirm v-if="showModal" :id="itemId"></confirm>
    </div>
  </div>
</template>

<script>
import TitleBar from "./../shared/TitleBar.vue";
import { api } from "../../config";
import { Event } from "../../app.js";
import { siteUrl } from "../../config";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import Confirm from "./../shared/Confirm.vue";

export default {
  components: {
    "title-bar": TitleBar,
     Loading,
    'confirm' : Confirm
  },
  data() {
    return {
      noItem: true,
      items: [],
      siteUrl: siteUrl,
      path: "/uploads/images/",
      isLoading: false,
      fullPage: true,
      showModal : false ,
      itemId: "",
      resultedData : '', 
      searchedItem: ''
    };
  },
  methods: {
    getItems() {
      axios.get(api.getItems).then(res => {
        const results = [];
        for (let key in res.data) {
          results.push(res.data[key]);
          for (var i = 0; i < results[key].images.length; i++) {
            if (results[key].images[i].primary_image == 1) {
              results[key].image = results[key].images[i].url;
            }
          }
        }
        this.items = results;
        this.isLoading = false;
        if (this.items.length > 0) {
          this.noItem = true;
        } else {
          this.noItem = false;
        }
      });
    },
    editItem(id) {
      this.$router.push({
        name: "form.Edit",
        params: { id: id }
      });
    },
    hideModal(){
      this.showModal= false;
      this.getItems();
    },
    confirmDelete(id) {
      this.showModal = true;
      this.itemId = id ;
    },
    clearSearchBar(clearItem) {
       if(!this.resultedData) {
          this.resultedData = this.items
        }
        clearItem = this.searchedItem;
        if(clearItem === '') {
           this.items = this.resultedData;
          this.noItem = true
        }
        this.items = this.resultedData;
     }
  },
  created() {
    this.getItems();
  },
  mounted() {
   
    Event.$on("item-deleted", action => {
      this.hideModal();
    });

    Event.$on("cancel-modal", action => {
      this.showModal= false;
    });

    //to clear search with  filter icon
    Event.$on("show-filter-bar", (clearItem) => {
      this.showFilter = true;
      this.clearSearchBar(clearItem);
    });


    // to clear search bar
    Event.$on("clearSearch-Bar" , clearItem => {
      this.clearSearchBar(clearItem);
    });

    
    //search item
    Event.$on("searchItem", searchItem => {
        let searchData = searchItem.toLowerCase(),existingItems;
        if(!this.resultedData) {
            this.resultedData = this.items
            existingItems = this.items;
         }else{
            existingItems = this.resultedData;
         }
         
        if(searchData !=='') {
          let filteredItem = existingItems.filter(data => {
           if(data.stock_number.toLowerCase().indexOf(searchData) > -1 ||
               data.stock_number.match(searchData) )
               {
                  return data;  
               }         
           })
            this.items = filteredItem;
            this.isLoading = false;
            if (this.items.length > 0) {
                this.noItem = true;
             } else {
                 this.noItem = false;
             }         
        } else {
           this.items = this.resultedData;
           this.isLoading = false;
             if (this.items.length > 0) {
                this.noItem = true;
             } else {
                this.noItem = false;
             }
        }                    
      });

  }
};
</script>
<style scoped>
a.btn.btn--primary.add {
  display: block;
  float: right;
}
</style>
