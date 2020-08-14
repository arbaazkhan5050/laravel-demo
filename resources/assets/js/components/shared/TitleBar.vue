<template>
  <div class="title-bar">
    <h1>{{ title }}</h1>

    <div class="actions-wrapper">
    <!-- for search bar -->
      <div class="search-box" id="search-box" v-show="showSearchBar">
        <span style="position:relative;">
          <input type="text"  placeholder="Enter Name" 
          v-model.trim="searchItem" @keyup="searchItem" >
          <svg id="search-icon"  viewBox="0 0 24 24">
           <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
           </svg>
        </span> 
         <!-- <span>
        <svg id="search-icon"  viewBox="0 0 24 24">
           <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
           </svg>
        </span> -->
      </div>
      <span v-if="hasFilter" class="filter-toggle" @click="showFilter">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M12 12l8-8V0H0v4l8 8v8l4-4v-4z" />
        </svg>
      </span>
      <slot name="button"></slot>
    </div>
  </div>
</template>

<script>
import { Event } from "../../app.js";

export default {
  props: {
    title: {
      type: String,
      required: true
    },
    hasFilter: {
      type: Boolean
    },
    showButton: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      filterVisible: false,
      searchItem: '',
      showSearchBar : true
    };
  },
  

  methods: {
    showFilter() {
      if (!this.filterVisible) {
        Event.$emit("show-filter-bar");
        this.filterVisible = true;
        this.searchItem= ''
        
      } else {
        Event.$emit("close-filter-bar");
        this.filterVisible = false;        
      }
    },
    searchItem() {
      Event.$emit("searchItem", this.searchItem);
    },
    closeFilter() {
       if(this.filterVisible) {        
         Event.$emit("close-filter-bar");
         this.filterVisible = false;   
         Event.$emit("clearSearch-Bar", this.searchItem);
       }
     }
  }
};
</script>

<style lang="scss" scoped>
.actions-wrapper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
.filter-toggle {
  margin-right: 15px;
  display: none;
  align-items: center;
  justify-content: center;
  &:hover {
    cursor: pointer;
  }
  svg {
    width: 17px;
  }
}
@media (min-width: 1150px) {
  .filter-toggle {
    display: flex;
  }
}

/* for search bar and clear searh*/

.clear-search{
  cursor: pointer;
  font-size: 15px;
}
.search-box{
  padding: 5px 8px;
    display: inline;
    border-radius: 8px;
    background: border-box;
}
.search-box input {
  width: 400px;
}


.search-box span{
  margin-right: 15px;
}
#search-icon {
    height: 28px;
    position: absolute;
    right: 10px;
    top: -4px;
}
</style>
