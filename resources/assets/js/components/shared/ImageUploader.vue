<template>
  <div>
    <label>Upload Photos</label>
    <div class="invalid-feedback form-error error-color" v-show="error">{{ error }}</div>
    <form enctype="multipart/form-data" novalidate v-show="isInitial">
      <div class="dropbox">
        <div v-if="!hide" class="dropbox-desc-wrapper">
          <div class="image-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
              <path
                d="M0 4c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm11 9l-3-3-6 6h16l-5-5-2 2zm4-4a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"
              />
            </svg>
          </div>
          <p v-if="isInitial">
            Drag your image(s) here or
            <strong>Browse</strong>
          </p>
          <span>Supports JPG, JPEG & PNG</span>
        </div>
        <input
          ref="pictureInput"
          type="file"
          multiple
          :name="uploadFieldName"
          :disabled="isSaving"
          @change="
						filesChange($event.target.name, $event.target.files);
						fileCount = $event.target.files.length;
					"
          accept="image/*"
          class="input-file"
        />
      </div>
    </form>
    <div v-if="isSaving">
      <div class="dropbox dropbox_saving">
        <p>Uploading {{ fileCount }} files...</p>
        <tractor-loader></tractor-loader>
      </div>
    </div>
    <!--SUCCESS-->
    <div v-if="showBox">
      <div class="dropbox dropbox__images">
        <p>{{ uploadedFiles.length }} image(s) were uploaded successfully!</p>
        <p class="small">Click on an image to select it as the Featured Image.</p>
        <ul class="image-list">
          <li
            v-for="(item, idx) in uploadedFiles"
            :key="idx"
            :class="{ 'featured-container': featuredImage == item }"
          >
            <div class="featured-check" v-if="featuredImage == item">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                  d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"
                />
              </svg>
            </div>
            <div class="delete-icon" @click="removePhoto(idx , 'new')">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                  d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"
                />
              </svg>
            </div>
            <img
              :src="item.url"
              class="img-thumbnail"
              :alt="item.originalName"
              @click="setFeatured(item, 'new' , idx)"
              :class="{'featured' : featuredImage == item}"
            />
          </li>
        </ul>
        <a class="upload-reset" href="javascript:void(0)" @click="reset(),reSetImageArr()">Upload More Images</a>
      </div>
    </div>
   
   
     <!--Edit Images-->
    <div v-if="isEdit">
      <div class="dropbox dropbox__images">
        <ul class="image-list">
          <li
            v-for="(image, id) in previousImages"
            :key="id"
            :class="{ 'featured-container': featuredImage == image.url }"
          >
            <div class="featured-check" v-if="featuredImage == image.url">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                  d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"
                />
              </svg>
            </div>
            <div class="delete-icon" @click="removePhoto(id , 'old' ,image.id)">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                  d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"
                />
              </svg>
            </div>
            <img
              :src="siteUrl + path + image.url"
              class="img-thumbnail"
              @click="setFeatured(image.url , 'old' ,id)"
              :class="{'featured' : featuredImage == image.url}"
              onError="this.onerror=null; this.src='https://dummyimage.com/300x360/fff/aaa';"
            />
          </li>
        </ul>
      </div>
    </div>
    <!--FAILED-->
    <div v-if="isFailed">
      <h2>Uploaded failed.</h2>
      <p>
        <a href="javascript:void(0)" @click="reset(),reSetImageArr()">Try again</a>
      </p>
      <pre>{{ uploadError }}</pre>
    </div>
    
  </div>
</template>

<script>
// swap as you need
import { upload } from "../../file-upload.fake.service"; // fake service
import { wait } from "../../utils";
import { gsap } from "gsap";
import TractorLoader from './TractorLoader.vue';
import {Event} from '../../app.js';
import { siteUrl } from "../../config";
import { api } from "../../config";
let self = this;
const STATUS_INITIAL = 0,
  STATUS_SAVING = 1,
  STATUS_SUCCESS = 2,
  STATUS_FAILED = 3;

export default {
  components: {
    "tractor-loader": TractorLoader
  },
  data() {
    return {
      uploadedFiles: [],
      hide : false,
      showBox : false,
      imageStorage: [],
      uploadError: null,
      currentStatus: STATUS_INITIAL,
      uploadFieldName: "photos",
      featuredImage: null,
      form: "",
      error:"",
      images:[],
      isEdit: false,
      previousImages:[],
      siteUrl: siteUrl,
      path: "/uploads/images/"
    };
  },
  computed: {
    isInitial() {
      return this.currentStatus === STATUS_INITIAL;
    },
    isSaving() {
      return this.currentStatus === STATUS_SAVING;
    },
    isSuccess() {
      return this.currentStatus === STATUS_SUCCESS;
    },
    isFailed() {
      return this.currentStatus === STATUS_FAILED;
    }
  },
  methods: {
    setFeatured(image, type , idx) {
      this.featuredImage = image;
      if (type == 'new') {
        Event.$emit('featured-image' , { name: image.originalName , type: 'new' , id: idx})
      } else {
        Event.$emit('featured-image' , { name: image, type: 'old' , id: idx})
      }
    },
    removePhoto(idx, data ,imageId) {
      if (data == 'old') {
        this.previousImages.splice(idx, 1);

        //call delete image Api here
        let type = 'image';

        axios.delete(api.deleteImage + imageId + '/' + type)
          .then(res => {
            this.$noty.success('Image has been deleted!');
          })

      }else 
      {
        this.uploadedFiles.splice(idx, 1);

        let imageDelete = [];
        for (let i = 0; i < this.imageStorage.length; i++){
          if (idx !== i) {
            imageDelete.push(this.imageStorage[i]);
          }
        }
        this.imageStorage = imageDelete;
        Event.$emit('image-delete' , { id: idx});

        if (this.imageStorage.length < 1) {
          Event.$emit('reset-images');
        }
        this.filesChange();
      }
    },
    reSetImageArr(){
      this.hide = true;
      this.$nextTick(() => {
        this.$refs.pictureInput.click();
      });
         
    },
    reset() {
      this.uploadError = null;
    },
    save(formData) {
      // upload data to the server
      this.currentStatus = STATUS_SAVING;
      upload(formData)
        .then(wait(2500)) // DEV ONLY: wait for 2.5s
        .then(x => {
          this.uploadedFiles = [].concat(x);
          this.currentStatus = STATUS_SUCCESS;
          this.showBox = true;
        })
        .catch(err => {
          this.showBox = false;
          this.uploadError = err.response;
          this.currentStatus = STATUS_FAILED;
        });
    },
    filesChange(fieldName, fileList) {
      // handle file changes
     if (fileList) {
        if (!fileList.length) return;
        if (this.imageStorage.length > 0) {
          Event.$emit('reset-images')
          const formData = new FormData();
          Array.from(Array(fileList.length).keys()).map(x => {
            formData.append(fieldName, fileList[x], fileList[x].name);
          });
          Array.from(Array(this.imageStorage.length).keys()).map(x => {
            formData.append(
              fieldName,
              this.imageStorage[x],
              this.imageStorage[x].name
            );
          });
          this.save(formData);
       
         let arr = [];
          if(fileList.length > 0){
            for(let i = 0; i < fileList.length; i++){
              fileList[i]
              arr.push(fileList[i])
            }
          }

            for(let j = 0; j < this.imageStorage.length; j++){
              arr.push(this.imageStorage[j])
            }
           this.imageStorage = arr;
        } else {
          const formData = new FormData();
          this.imageStorage = fileList;
          // append the files to FormData
          Array.from(Array(fileList.length).keys()).map(x => {
            formData.append(fieldName, fileList[x], fileList[x].name);
          });
          this.save(formData);
        }
        
      }
    }
  },
  mounted() {
    this.reset();
    Event.$on('empty-image', (error) => {
            this.error = error.error;
    });
    Event.$on('edit-images', (images) => {
            this.isEdit = true ;
            for (let key in images.images){
            if (images.images[key].primary_image == 1) {
              this.featuredImage = images.images[key].url;
            }
            this.previousImages.push(images.images[key]);
          }
      });
  }
};
</script>

<style lang="scss" scoped>

.invalid-feedback{
  display: block !important;
}

.error-color {
  color: red;
}

.dropbox {
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  box-shadow: 1px 2px 7px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  position: relative;
  padding: 35px 50px;
  min-height: 185px;
  margin-top: 10px;
  //remove the hover style for saving and uploaded states
  &.dropbox__images,
  &.dropbox_saving {
    &:hover::before {
      background: transparent;
      border-color: #e5e5e5;
    }
    p {
      font-size: 1rem;
      font-weight: 600;
      text-align: center;
      line-height: 1.25;
      margin-bottom: 5px;
      &.small {
        font-size: 0.85rem;
        margin-bottom: 15px;
      }
    }
  }
  .dropbox-desc-wrapper {
    position: relative;
    // z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  &:hover::before {
    background: #fff7d4;
    border-color: #edc71f;
  }
  &:before {
    content: "";
    position: absolute;
    top: 10px;
    right: 10px;
    left: 10px;
    bottom: 10px;
    border-radius: 8px;
    border: 1px dashed #c6c6c6;
  }
  .image-icon {
    margin-bottom: 10px;
    svg {
      width: 60px;
    }
  }
  p {
    font-size: 1.1rem;
    font-weight: 600;
    strong {
      color: #ffd100;
    }
    & + span {
      font-size: 0.85rem;
      color: #9a8f8f;
      display: block;
      margin-top: 10px;
    }
  }
}

.input-file {
  opacity: 0; /* invisible but it's there! */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  z-index: 2;
  cursor: pointer;
}

ul.image-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
  grid-auto-rows: 135px;
  grid-gap: 15px;
  position: relative;
  z-index: 2;
  list-style: none;
  li {
    box-shadow: 0 0.5px 1.6px rgba(0, 0, 0, 0.04),
      0 4px 17px rgba(0, 0, 0, 0.08);
    border-radius: 8px;
    position: relative;
    height: 100%;
    width: 100%;
    max-height: 150px;
    &:hover {
      .delete-icon {
        opacity: 1;
      }
    }
    &.featured-container {
      box-shadow: 0 0.5px 1.6px rgba(0, 0, 0, 0.04),
        0 4px 17px rgba(0, 0, 0, 0.08), 0 0 0 4px #ffd100;
    }
    .featured-check {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 3;
      svg {
        width: 20px;
        fill: #fff;
      }
    }
    img {
      border-radius: 8px;
      object-fit: cover;
      height: 100%;
      width: 100%;
      filter: grayscale(0.8);
      transition: 0.15s;
      &.featured {
        filter: grayscale(0);
      }
    }
  }
}
.upload-reset {
  text-align: center;
  display: block;
  position: relative;
  z-index: 3;
  margin-top: 25px;
  text-decoration: underline;
  font-size: 0.8rem;
  text-transform: uppercase;
  font-weight: 600;
  color: #ffd100;
}

.dropbox_saving {
  p {
    position: relative;
    z-index: 1;
    padding-top: 15px;
  }
  .progress-bar {
    position: absolute;
    background: transparent;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    &:before {
      content: "";
      background: #f9f9f9;
      width: 100%;
      height: 100%;
      position: absolute;
      transform: translateX(-105%);
      transition: 2.5s linear;
    }
    span {
      display: block;
      position: relative;
      height: 4px;
      width: 80%;
      margin: 0 auto;
      background: #fff7d3;
      overflow: hidden;
      &:after {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: #ffd100;
        transform: translateX(-105%);
        transition: 2s linear;
        transition-delay: 0.25s;
      }
    }
  }
}

.delete-icon {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 3;
  opacity: 0;
  transition: 0.15s;
  svg {
    width: 20px;
    fill: #780216;
  }
}
</style>

