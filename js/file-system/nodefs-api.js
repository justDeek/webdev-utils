import fs from 'node:fs';
import * as fsPath from "node:path";

const isEmpty = (variable) => typeof variable === 'undefined';
const join = (...paths) => fsPath.join(paths);
export const getFileName = (path) => fsPath.basename(path); //('projects/web/index.js' => index.js)
export const getFolderName = (path) => fsPath.dirname(path);
export const getFileExtension = (path) => fsPath.extname(path);

const exclude = ['_assets'];
const docsFolder = ".docs";
const docsRoot = join(process.cwd(), docsFolder);

//make sure the docsRoot exists
export function init() {
  createDir(docsRoot);
}

//get list of files and directories from given dirPath and all it's sub directories
//based on: https://stackoverflow.com/a/58040005/13237335
export function getDirectoryContent(dirPath) {
  const dirSep = "\\";
  let result = [];
  
  if (isEmpty(dirPath)) return result;
  
  if (!fs.existsSync(dirPath)) {
    console.warn("Path does not exist: " + dirPath);
    return result;
  }

  let directoryList = [];
  if (dirPath[dirPath.length -1] !== dirSep) dirPath = dirPath + dirSep;

  directoryList.push(dirPath);

  while (directoryList.length > 0) {
    let currDir = directoryList.shift();
    if (!(fs.existsSync(currDir) && fs.lstatSync(currDir).isDirectory())) continue;

    let currDirContent = fs.readdirSync(currDir);
    while (currDirContent.length > 0) {
      let currObj = currDirContent.shift();
      let objPath = currDir + currObj;

      if (!fs.existsSync(objPath)) continue;
      if (fs.lstatSync(objPath).isDirectory()) {
        if (exclude.some(ex => currObj.startsWith(ex))) continue;
        
        let currDirPath = objPath + dirSep;
        directoryList.push(currDirPath);
        result.push(currDirPath);
      } else {
        result.push(objPath);
      }
    }
  }
  
  return result.map(r => r.replace(docsRoot, '').replaceAll(dirSep, '/').sort());
}

export function createDir(relPath) {
  let path = join(docsRoot, relPath);

  try {
    if (!fs.existsSync(path)) fs.mkdirSync(path);
  } catch (err) {console.error(err)}
}

export function removeDir(relPath) {
  let path = join(docsRoot, relPath);

  try {
    if (fs.existsSync(path)) fs.rmdirSync(path);
  } catch (err) {console.error(err)}
}

export function writeFile(relPath, content) {
  let path = join(docsRoot, relPath);

  try {
    fs.writeFileSync(path, content);
  } catch (err) {console.error(err)}
}

export function readFile(relPath) {
  let path = join(docsRoot, relPath);

  try {
    fs.readFileSync(path, "utf8");
  } catch (err) {console.error(err)}
}

export function removeFile(relPath) {
  let path = join(docsRoot, relPath);

  try {
    if (fs.existsSync(path)) fs.unlinkSync(path);
  } catch (err) {console.error(err)}
}

export function renameFileOrFolder(oldRelPath, newRelPath) {
  let oldPath = join(docsRoot, oldRelPath);
  let newPath = join(docsRoot, newRelPath);

  try {
    fs.renameSync(oldPath, newPath);
  } catch (err) {console.error(err)}
}