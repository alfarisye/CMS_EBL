// ===================================================================
// importScripts('https://unpkg.com/axios@0.21.3/dist/axios.min.js'); // apabila axios dalam dev nya 
// importScripts('https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js');
// taruh axios dan crypto js min di index root
// ===================================================================
let urlnya = "https://officialapp.my.id/";
// let urlnya = "http://localhost/web_harp/";
class myplugin {
  route="";
  url = urlnya+this.route;
  table = null;
  id = null;
  popmsg = true;
  primary=null;
  path="api/auto";

  constructor(framework='ci',url=this.url,popmsg=true) {
      if(framework=='ci'){
        this.path="api/auto"
      }else if(framework=='php'){
        this.path="api/Auto.php"
      }
      this.url=url;
      this.popmsg=popmsg;
  }

  collection(table,popmsg=true) {
    this.popmsg=popmsg;
    this.table = table;
    return this;
  }

  doc(id = -100, primary = null) {
    this.id = id;
    this.primary = primary;
    return this;
  }

  async set(vdata,validation=0) {
    if (!this.table) {
      this.alert('Error Table');
      return;
    }
    let data = {
      ...vdata,
      t_a: this.table, // table
      i_a: this.id, // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'set' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+''+`?validation=${validation}`, data, this.get_option()).then(res => {
      if(res.data.status===false){
        popupMsg('User Authentikasi telah berakhir! Silahkan login kembali', 'bg-red-600')
        window.location.replace(this.url);
        return;
      }
      if (this.popmsg) {
        popupMsg('Set data berhasil !', 'bg-green-600')
      }
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    })
  }

  async delete() {
    if (!this.table) {
      this.alert('Error Table');
      return ;
    }
    if(!this.id){
      this.alert('Error ID Tidak ada');
      return ;
    }
    let data = {
      t_a: this.table, // table
      i_a: this.id, // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'delete' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+'', data, this.get_option()).then(res => {
      if(res.data.status===false){
        popupMsg('User Authentikasi telah berakhir! Silahkan login kembali', 'bg-red-600')
        window.location.replace(this.url);
        return;
      }
      if (this.popmsg) {
        popupMsg('Proses delete berhasil!', 'bg-red-600')
      }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    })
  }

  async select(query) {
    if(!query){
      this.alert('Error query');
      return;
    }
    let data = {
      t_a: this.table, // table
      i_a: '', // id
      p_a: this.primary, // primary key
      q_a: this.scramble(query), // query
      f_a: 'select' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+'', data, this.get_option()).then(res => {
      if(res.data.status===false){
        popupMsg('User Authentikasi telah berakhir! Silahkan login kembali', 'bg-red-600')
        window.location.replace(this.url);
        return;
      }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Select Gagal!', 'bg-red-600')
      }
    })
  }

  async get($get='') {
    let data = {
      t_a: this.table, // table
      i_a: '', // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'get' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+''+`?get=${$get}`, data, this.get_option()).then(res => {
      if(res.data.status===false){
        popupMsg('User Authentikasi telah berakhir! Silahkan login kembali', 'bg-red-600')
        window.location.replace(this.url);
        return;
    }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Select Gagal!', 'bg-red-600')
      }
    })
  }

  async pagination(pagination,urlstring) {
    if(!pagination){
      this.alert('Error query');
      return;
    }
    let data = {
      t_a: this.table, // table
      i_a: '', // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'pagination' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+`?query=${pagination}&other=pagination&${urlstring}`, data, this.get_option()).then(res => {
      if(res.data.status===false){
          popupMsg('User Authentikasi telah berakhir! Silahkan login kembali', 'bg-red-600')
          window.location.replace(this.url);
          return;
      }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Select Gagal!', 'bg-red-600')
      }
    })
  }

  async login(vdata) {
    if(!vdata.username){
      this.alert('Error username tidak ada');
      return;
    }
    if(!vdata.password){
      this.alert('Error password tidak ada');
      return;
    }
    let data = {
      ...vdata
    }
    return await axios.post(this.url + this.path+'?other=login', data).then(res => {
      if (this.popmsg) {
        // popupMsg('Login Berhasil!', 'bg-green-600')
      }
      localStorage.setItem('xrf-token', res.data.token);
      localStorage.setItem('users', this.scramble(JSON.stringify(res.data.users[0])));
      return res;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg(err.response.data.message, 'bg-red-600')
      }
    })
  }

  async register(vdata,update=false) {
    let data = {
      ...vdata
    }
    return await axios.post(this.url + this.path+`?other=register${update?'&update=true':''}`, data, this.get_option()).then(res => {
      if (this.popmsg) {
        popupMsg('Proses Register Berhasil!', 'bg-green-600')
      }
      return true;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg(err.response.data.message, 'bg-red-600')
      }
    })
  }

  async ceklogin() {
    return await axios.post(this.url + this.path+'?other=ceklogin', {}, this.get_option()).then(res => {
      // route ke mana 
      if (this.popmsg) {
        // popupMsg("Login Ter authentikasi", 'bg-green-600')
      }
      return res.data;
    }).catch(err => {
      // route ke mana
        popupMsg("Login Tidak authentikasi", 'bg-red-600')
        return false;
    })
  }

  async logout() {
    localStorage.removeItem('xrf-token');
    return await axios.post(this.url + this.path+'?other=logout', {}, this.get_option()).then(res => {
      if (this.popmsg) {
        popupMsg('Proses logout Berhasil!', 'bg-green-600')
      }
      return true;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses logout gagal!', 'bg-red-600')
      }
    })
  }

  async current_user(store) {
    if(!localStorage.getItem('users')){
        popupMsg('Error User tidak ada!', 'bg-red-600')
        return ;
    }
    let id_user=JSON.parse(this.scramble(localStorage.getItem('users'))).id_user;
    return await axios.post(this.url + this.path+`?other=current_user&id_user=${id_user}`, {}, this.get_option()).then(res => {
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error User tidak ada!', 'bg-red-600')
      }
    })
  }

  async remove() {
    if (!this.table) {
      this.alert('Error Table');
      return ;
    }
    if(!this.id){
      this.alert('Error ID Tidak ada');
      return ;
    }
    let data = {
      t_a: this.table, // table
      i_a: this.id, // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'remove' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+'', data, this.get_option()).then(res => {
      if (this.popmsg) {
        popupMsg('Remove data berhasil!', 'bg-red-600')
      }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    })
  }

  async cektrash() {
    if (!this.table) {
      this.alert('Error Table');
      return ;
    }
    if(!this.id){
      this.alert('Error ID Tidak ada');
      return ;
    }
    let data = {
      t_a: this.table, // table
      i_a: this.id, // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'cek_trash' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+'', data, this.get_option()).then(res => {
      if (this.popmsg) {
        popupMsg('get berhasil!', 'bg-green-600')
      }
      return res.data;
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    })
  }

  async restore() {
    if (!this.table) {
      this.alert('Error Table');
      return ;
    }
    if(!this.id){
      this.alert('Error ID Tidak ada');
      return ;
    }
    let data = {
      t_a: this.table, // table
      i_a: this.id, // id
      p_a: this.primary, // primary key
      q_a: '', // query
      f_a: 'restore' // function , set,delete,select,remove,cek_trash,restore,pagination
    }
    return await axios.post(this.url +this.path+'', data, this.get_option()).then(res => {
      if (this.popmsg) {
        popupMsg('Proses Restore Berhasil!', 'bg-green-600')
      }
      return "proses restore berhasil!";
    }).catch(err => {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    })
  }
  

  async upload(file, compres = false, scrambles = true) {
    let that = this;
    let el = file; // berikan id pada input file
    const options = {
      maxSizeMB: 1,
      maxWidthOrHeight: 1920,
      useWebWorker: true
    }
    try {
      let file = el.files[0];
      let fd = new FormData();
      // if(compres){
      //   await importScripts2('https://cdn.jsdelivr.net/npm/browser-image-compression@1.0.14/dist/browser-image-compression.min.js');
      //   const compressedFile = await imageCompression(file, options);
      //   fd.append("file", compressedFile);
      // }else{
      fd.append("file", file);
      fd.append('secret',scramble('hit'));
      // }
      return await axios.post(urlnya+'/assets/upload.php',fd).then(res=>{
        return res.data;
      })
    } catch (error) {
      if (this.popmsg) {
        popupMsg('Error Proses Gagal!', 'bg-red-600')
      }
    }
  }

  get_option() {
    return {
      'headers': localStorage.getItem('xrf-token') ? {
        'Authorization': 'bearer ' + localStorage.getItem('xrf-token')+'xyz' 
      }: false
    }
  }





  // =========================================== FUNCTIONAL
  alert(text, color = 'bg-red-600') {
    popupMsg(text, color);
  }
  async confirm(text, color = 'bg-red-600') {
    await new Promise((resolve, reject) => {
      confirmAdd(text, resolve);
    });
    confirmRemove();
    return jawabanconfirm;
  }
  loadingOn() {
    loadingScreenAdd();
  }
  loadingOff() {
    loadingScreenRemove();
  }
  scramble(datas) {
    let data = scramble(datas);
    return data
  }

}


// = = = = = = GLOBAL FUNCTION

function popupMsg(txt, color) {
  let div = document.createElement('div')
  div.setAttribute('id', 'popup-msg')
  div.setAttribute('style', 'position:fixed;top:30px;right:30px;z-index:1000000;')
  div.setAttribute('class', `rounded-lg shadow ${color} text-white animated fadeInDown p-3`);
  div.addEventListener('click', (e) => {
    console.log(e.target.remove())
  })
  div.innerHTML = txt;
  document.querySelector('body').appendChild(div);
  setTimeout(() => {
    document.querySelector('#popup-msg').classList.remove('fadeInDown');
    document.querySelector('#popup-msg').classList.add('fadeOutUp');
    setTimeout(() => {
      document.querySelector('#popup-msg').remove();
    }, 1000);
  }, 2000);
}

function loadingScreenAdd(txt, color) {
  let div = document.createElement('div')
  let div2 = document.createElement('div')
  div.setAttribute('id', 'loadingScreen')
  div.setAttribute('style', 'position:fixed;top:0;left:0;z-index:10000;height:100vh;width:100vw;background:rgb(0,0,0,0.5)')
  div.setAttribute('class', ` animated fadeIn d-flex justify-content-center align-items-center`);
  div2.setAttribute('id', 'loadingScreen2')
  div2.setAttribute('class', `text-xl text-white font-bold text-center italic lds-ripple`);
  div2.innerHTML = '<div class=""></div><br><br><br><p class="text-center"> Loading </p>';
  div.appendChild(div2);
  document.querySelector('body').appendChild(div);

}

function loadingScreenRemove() {
  document.querySelector('#loadingScreen').classList.remove('fadeIn');
  document.querySelector('#loadingScreen').classList.add('fadeOut');
  setTimeout(() => {
    document.querySelector('#loadingScreen').remove();
  }, 500);
}

function jikayes() {
  alert('woke')
}

function jikano() {
  alert('no')
}

async function confirmAdd(txt, resolve, color) {
  let div = document.createElement('div')
  let div2 = document.createElement('div')
  let divrow1 = document.createElement('div')
  let divrow2 = document.createElement('div')
  let divcol6p1 = document.createElement('div')
  let divcol4p1 = document.createElement('div')
  let divcol4p2 = document.createElement('div')
  let divcol4p3 = document.createElement('div')
  let buttonx = document.createElement('button')
  let buttonyes = document.createElement('button')
  let buttonno = document.createElement('button')
  let ptext = document.createElement('p')
  let garis = document.createElement('hr')
  div.setAttribute('id', 'confirmScreen')
  div.setAttribute('style', 'position:fixed;top:0;left:0;z-index:10000;height:100vh;width:100vw;background:rgb(0,0,0,0.5)')
  div.setAttribute('class', ` animated fadeIn d-flex justify-content-center align-items-center`);
  div2.setAttribute('id', 'confirmScreen2')
  div2.setAttribute('class', ``);
  divrow1.setAttribute('class', 'row justify-content-center')
  divrow1.setAttribute('style', 'width:100vw;')
  divrow2.setAttribute('class', 'row justify-content-between')
  divcol6p1.setAttribute('class', 'animate__animated animate__backInDown bg-white rounded-lg p-3 shadow col-10 col-sm-6')
  divcol6p1.setAttribute('style', 'min-height:35h;')
  divcol4p1.setAttribute('class', 'offset-2 col-4')
  divcol4p2.setAttribute('class', 'col-4')
  divcol4p3.setAttribute('class', 'col-2')
  buttonx.setAttribute('class', 'btn btn-sm btn-dark  float-right')
  buttonx.innerHTML = 'x'
  buttonyes.setAttribute('class', 'btn btn-sm btn-success  btn-block')
  buttonyes.innerHTML = '<span class="typcn typcn-tick"></span> Yes'
  buttonno.setAttribute('class', 'btn btn-sm btn-danger  btn-block')
  buttonno.innerHTML = '<span class="typcn typcn-cancel"></span> No'
  ptext.setAttribute('class', 'font-semibold text-xl')
  ptext.innerHTML = `${txt}`;
  divcol4p2.appendChild(buttonno);
  divcol4p1.appendChild(buttonyes);
  divrow2.appendChild(divcol4p1);
  divrow2.appendChild(divcol4p2);
  divrow2.appendChild(divcol4p3);
  divcol6p1.appendChild(buttonx);
  divcol6p1.appendChild(ptext);
  divcol6p1.appendChild(garis);
  divcol6p1.appendChild(divrow2);
  divrow1.appendChild(divcol6p1);
  div2.appendChild(divrow1)
  div.appendChild(div2);
  buttonx.addEventListener('click', function () {
    jawabanconfirm = false;
    resolve('no');
  })
  buttonyes.addEventListener('click', function () {
    jawabanconfirm = true;
    resolve('yes');
  })
  buttonno.addEventListener('click', function () {
    jawabanconfirm = false;
    resolve('no');
  })
  document.querySelector('body').appendChild(div);
}

function confirmRemove() {
  document.querySelector('#confirmScreen').classList.remove('fadeIn');
  document.querySelector('#confirmScreen').classList.add('fadeOut');
  setTimeout(() => {
    document.querySelector('#confirmScreen').remove();
  }, 1000);
}

function scramble(teks) {
  let char = "";
  let hasil = "";
  for (var i = 0; i < teks.length; i++) {
    char = "";
    char = teks.charAt(i);
    if (char == "a") {
      char = char.replace("a", "x");
    } else if (char == "b") {
      char = char.replace("b", "v");
    } else if (char == "c") {
      char = char.replace("c", "u");
    } else if (char == "d") {
      char = char.replace("d", "w");
    } else if (char == "e") {
      char = char.replace("e", "y");
    } else if (char == "f") {
      char = char.replace("f", "z");
    } else if (char == "g") {
      char = char.replace("g", "t");
    } else if (char == "h") {
      char = char.replace("h", "s");
    } else if (char == "i") {
      char = char.replace("i", "r");
    } else if (char == "j") {
      char = char.replace("j", "q");
    } else if (char == "k") {
      char = char.replace("k", "p");
    } else if (char == "l") {
      char = char.replace("l", "o");
    } else if (char == "m") {
      char = char.replace("m", "n");
    } else if (char == "z") {
      char = char.replace("z", "f");
    } else if (char == "y") {
      char = char.replace("y", "e");
    } else if (char == "x") {
      char = char.replace("x", "a");
    } else if (char == "w") {
      char = char.replace("w", "d");
    } else if (char == "v") {
      char = char.replace("v", "b");
    } else if (char == "u") {
      char = char.replace("u", "c");
    } else if (char == "t") {
      char = char.replace("t", "g");
    } else if (char == "s") {
      char = char.replace("s", "h");
    } else if (char == "r") {
      char = char.replace("r", "i");
    } else if (char == "q") {
      char = char.replace("q", "j");
    } else if (char == "p") {
      char = char.replace("p", "k");
    } else if (char == "o") {
      char = char.replace("o", "l");
    } else if (char == "n") {
      char = char.replace("n", "m");
    } else if (char == "1") {
      char = char.replace("1", "0");
    } else if (char == "2") {
      char = char.replace("2", "8");
    } else if (char == "3") {
      char = char.replace("3", "9");
    } else if (char == "4") {
      char = char.replace("4", "7");
    } else if (char == "5") {
      char = char.replace("5", "6");
    } else if (char == "0") {
      char = char.replace("0", "1");
    } else if (char == "8") {
      char = char.replace("8", "2");
    } else if (char == "9") {
      char = char.replace("9", "3");
    } else if (char == "7") {
      char = char.replace("7", "4");
    } else if (char == "6") {
      char = char.replace("6", "5");
    } else if (char == " ") {
      char = char.replace(" ", "_");
    } else if (char == "_") {
      char = char.replace("_", " ");
    } else if (char == "*") {
      char = char.replace("*", "/");
    } else if (char == "/") {
      char = char.replace("/", "*");
    } else if (char == ",") {
      char = char.replace(",", "^");
    } else if (char == "^") {
      char = char.replace("^", ",");
    }
    hasil = hasil + char;
  }
  return hasil;
}

function importScripts(scripts) {
  var script = document.createElement('script');
  script.src = scripts;
  document.body.appendChild(script);
}

async function importScripts2(scripts) {
  return new Promise((resolve, reject) => {
    var script = document.createElement('script');
    script.src = scripts;
    document.body.appendChild(script);
    setTimeout(() => {
      resolve();
    }, 1000);
  });

}

let style = document.createElement('style')
style.innerHTML = `
.lds-ripple {
display: inline-block;
position: relative;
width: 80px;
height: 80px;
}
.lds-ripple div {
position: absolute;
border: 4px solid #fff;
opacity: 1;
border-radius: 50%;
animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
.lds-ripple div:nth-child(2) {
animation-delay: -0.5s;
}
@keyframes lds-ripple {
0% {
  top: 36px;
  left: 36px;
  width: 0;
  height: 0;
  opacity: 1;
}
100% {
  top: 0px;
  left: 0px;
  width: 72px;
  height: 72px;
  opacity: 0;
}
}
`;
document.head.appendChild(style);

export default myplugin