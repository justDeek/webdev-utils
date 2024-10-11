class Storage {
  get(key, defVal) {
    let result = localStorage.getItem(key);
    return result ? JSON.parse(result) : defVal;
  }
  
  set(key, val) {
    localStorage.setItem(key, JSON.stringify(val));
  }
}

export const storage = new Storage();