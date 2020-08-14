<template>
  <div class="modal" :class="{'open' : className}" data-modal="csv">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Upload File</h4>
        <span @click="clearDropZone" class="close-modal">&#215;</span>
      </div>
    <vue-dropzone  accept=".csv" ref="myVueDropzone" id="dropzone" v-on:vdropzone-upload-progress="dropzoneUploadProgress"  v-on:vdropzone-success="showSuccess" v-on:vdropzone-error="showError" v-on:vdropzone-complete="showComplete" :options="dropzoneOptions"  :useCustomSlot=true> 
      <div class="modal-body">
        <div class="upload-field">
          <svg xmlns="http://www.w3.org/2000/svg" width="49.277" height="42.361" viewBox="0 0 49.277 42.361">
            <g transform="translate(0 -35.93)">
              <g transform="translate(14.285 51.848)">
                <g transform="translate(0 0)">
                  <path class="a" style="fill: #636363"
                    d="M158.827,201.322l-10.4,10.4,2.717,2.717,5.763-5.763v19.088h3.842V208.677l5.763,5.763,2.717-2.717Z"
                    transform="translate(-148.426 -201.322)" />
                </g>
              </g>
              <g transform="translate(0 35.93)">
                <g transform="translate(0 0)">
                  <path class="a"
                    d="M40.05,51.064A15.563,15.563,0,0,0,9.242,48.392a11.72,11.72,0,0,0,2.476,23.175h2.017V67.724H11.708A7.877,7.877,0,0,1,11.08,52l1.595-.128.163-1.591a11.719,11.719,0,0,1,23.376,1.213v3.362H39a6.436,6.436,0,1,1,0,12.871H35.637v3.842H39a10.278,10.278,0,0,0,1.051-20.5Z"
                    transform="translate(0 -35.93)" />
                </g>
              </g>
            </g>
          </svg>
          <p>Drop your file here</p>
          <p>or</p>
          <button>Upload</button>
        </div>
      </div>
    </vue-dropzone>
    </div>
    <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>
  </div>
</template>
<script>
import {siteName} from './../../config';
import {siteUrl} from './../../config';
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
export default {
    props: ["className"],
	data() {
	   return {
        isLoading: false,
        fullPage: true,
        siteName: siteName,
        siteUrl : siteUrl,
        file : '',
        dropzoneOptions: {
          url: siteUrl + '/api/importFile',
          thumbnailWidth: 150,
          maxFilesize: 1000,
          headers: { "My-Awesome-Header": "header value" },
        }
	  }
    },
    components: {
        vueDropzone: vue2Dropzone,
        Loading
    },
    methods: {
       'dropzoneUploadProgress' : function (file, response) {
          this.isLoading = true;
       }, 
      'showSuccess': function (file, response) {
          this.isLoading = false;
          this.clearDropZone();
        },
       'showError': function (file, response) {
            this.clearDropZone();
        },
       'showComplete': function (file, response) {
           this.isLoading = false;
           this.clearDropZone();
           this.$noty.success("Products Added Successfully !", {
            closeWith: ["click", "button"]
          });
       },
      clearDropZone(){
         this.$refs.myVueDropzone.removeAllFiles()
      }
    },
    mounted(){}  
}
</script>
<style >
span.close-modal {
    display: block;
    font-size: 28px;
    float: right;
    margin-right: -16px;
    margin-top: -25px;
}

</style>
