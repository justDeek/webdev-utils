/** @file Initialize the browser-native eyedropper feature to pick the HEX-code for any color on the screen.
 * based on: https://www.kirupa.com/javascript/eyedropper_colorpicker.htm */

let eyeDropper = new EyeDropper();

export function openEyeDropper() {
  eyeDropper.open().then(result => {
    console.log(result.sRGBHex);
  }).catch(error => {
    console.log(error);
  });
}
