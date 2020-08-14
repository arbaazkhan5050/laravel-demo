import {Event} from './app.js';
import EXIF from 'exif-js';

function upload(formData) {
    const photos = formData.getAll('photos');
    const promises = photos.map((x) => getImage(x)
        .then(img => ({
            id: img,
            originalName: x.name,
            fileName: x.name,
            url: img
        })));
    return Promise.all(promises);
}

function getImage(file) {
    return new Promise((resolve, reject) => {
        const fReader = new FileReader();
        const img = document.createElement('img');
        let Orientation = ''; 

        const exif = EXIF.getData(file, function(

        ) {
          Orientation = this.exifdata.Orientation;
        });

        fReader.onload = () => {
            img.src = fReader.result;
            resetOrientation(img.src, Orientation, function(resetBase64Image) {
                resolve(resetBase64Image);
            });

        }
        if(file.type == 'application/pdf'){
            Event.$emit("document-upload" , { documents: file})
            Event.$emit("document-upload-edit" , { documents: file})

        }else{

            Event.$emit("image-upload", { images: file });
            Event.$emit("image-upload-edit", { images: file });
        }
        fReader.readAsDataURL(file);
    })
}

function resetOrientation(srcBase64, srcOrientation, callback) {
	var img = new Image();	

	img.onload = function() {
  	var width = img.width,
    		height = img.height,
        canvas = document.createElement('canvas'),
	  		ctx = canvas.getContext("2d");
		
    // set proper canvas dimensions before transform & export
	if (4 < srcOrientation && srcOrientation < 9) {
    	canvas.width = height;
      canvas.height = width;
    } else {
    	canvas.width = width;
      canvas.height = height;
    }

  	// transform context before drawing image
	switch (srcOrientation) {
      case 2: ctx.transform(-1, 0, 0, 1, width, 0); break;
      case 3: ctx.transform(-1, 0, 0, -1, width, height ); break;
      case 4: ctx.transform(1, 0, 0, -1, 0, height ); break;
      case 5: ctx.transform(0, 1, 1, 0, 0, 0); break;
      case 6: ctx.transform(0, 1, -1, 0, height , 0); break;
      case 7: ctx.transform(0, -1, -1, 0, height , width); break;
      case 8: ctx.transform(0, -1, 1, 0, 0, width); break;
      default: break;
    }

	  // draw image
      ctx.drawImage(img, 0, 0);

	  // export base64
	  callback(canvas.toDataURL());
  };

	img.src = srcBase64;
}

export { upload }