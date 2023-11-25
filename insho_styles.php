<style>
  /* ================================================ */
  /* INSHO CSS v.1.0.1 */
  /* ================================================ */
  a {text-decoration:none}
  code,.consolas{font-family:consolas !important}
  
  /* ================================================ */
  /* JUDUL */
  /* ================================================ */
  h1{font-size:32px; font-family:consolas}
  .subsistem{
    display: block;
    color: #f55;
    padding: 5px;
    margin: 15px 0 10px 0;
    font-family:consolas !important; 
    border-bottom: solid 1px #ccf; 
    background: #ffffffaa;
    text-transform: capitalize;
  }

  /* ================================================ */
  /* DEBUG */
  /* ================================================ */
  .hideit{display:none}
  .debug{
    background:red; 
    font-family: consolas; 
    font-size: small; 
    /* padding:10px;  */
    <?php if (!$dm) { echo 'display:none;'; } ?>
  }

  /* ================================================ */
  /* COLORING */
  /* ================================================ */
  .biru,.blue{color:blue}
  .merah,.red{color:red}
  .hijau,.green{color:green}
  .kuning,.yellow{color:orange}
  .ungu,.purple{color:purple}
  .abu,.gray{color:gray}
  .darkblue{color:darkblue}
  .darkred{color:darkred}


  /* ================================================ */
  /* BACKGROUND */
  /* ================================================ */
  .gradasi-biru{background:linear-gradient(#eef,#ccf)}
  .gradasi-toska{background:linear-gradient(#eff,#cff)}
  .gradasi-hijau{background:linear-gradient(#efe,#cfc)}
  .gradasi-merah{background:linear-gradient(#fee,#fcc)}
  .gradasi-kuning{background:linear-gradient(#ffe,#ffc)}
  .gradasi-pink{background:linear-gradient(#fdf,#faf)}
  .gradasi-tulang{background:linear-gradient(#ffe,rgb(213, 234, 233))}
  .bg-white,.bg-putih{background: white;}

  /* ================================================ */
  /* TEXT-STYLING */
  /* ================================================ */
  .tebal,.bold{font-weight:bold}
  .small,.kecil{font-size:small}
  .big,.besar{font-size:24px}
  .system {font-family:consolas}
  .upper {text-transform: uppercase}
  .lower {text-transform: lowercase}
  .proper {text-transform: capitalize}
  .miring {font-style: italic}

  /* ================================================ */
  /* BOX & NAVIGATION */
  /* ================================================ */
  .wadah{
    border: solid 1px #ccc;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 15px;
  }

  .wadah_active{
    border: solid 3px blue;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 15px;
    background: linear-gradient(#efe,#cfc);
  }

  .tr_active{
    border: solid 4px blue;
    font-weight:bold;
  }

  .navigasi a {display:inline-block; padding:5px 15px; background:linear-gradient(#eef,#cfc); min-width:50px;text-align:center; border-radius:10px;transition:.2s }
  .navigasi a:hover {background:linear-gradient(#eef,#fcf); letter-spacing:1px; text-transform:uppercase}

  /* ================================================ */
  /* FORM & TABLE */
  /* ================================================ */
  .judul-tabel{padding:5px; background: linear-gradient(#efc,#cfc); letter-spacing:2px; text-transform: uppercase}
  .form-group{margin-top: 10px}
  .kapital, .proper{text-transform:capitalize}
  .btn-block{width:100%}
  .input-keterangan{font-size:small; color:gray; margin:10px 0 10px 10px}
  /* ================================================ */
  /* EDITABLE */
  /* ================================================ */
  .editable{ background: linear-gradient(#efe,#cfc); cursor:pointer; transition:.2s}
  .editable:hover{ background: linear-gradient(#fef,#fcf); letter-spacing:1px}
  .deletable{ background: linear-gradient(#fee,#fcc); cursor:pointer; transition:.2s}
  .deletable:hover{ background: linear-gradient(#fef,#fcf); letter-spacing:1px}

  /* ================================================ */
  /* ANIMATED */
  /* ================================================ */
  .pointer, .closable{cursor: pointer;}
  .zoom, .img_zoom{transition:.2s}
  .zoom:hover, .img_zoom:hover{transform:scale(1.2); font-size:1.5em}
  
  /* ================================================ */
  /* MARGINS */
  /* ================================================ */
  .m0{margin: 0}
  .m1{margin: 5px}
  .m2{margin: 10px}
  .m3{margin: 15px}
  .m4{margin: 20px}

  .mb1{margin-bottom:5px}
  .mb2{margin-bottom:10px}
  .mb3{margin-bottom:15px}
  .mb4{margin-bottom:20px}
  .mt1{margin-top:5px}
  .mt2{margin-top:10px}
  .mt3{margin-top:15px}
  .mt4{margin-top:20px}
  .ml1{margin-left:5px}
  .ml2{margin-left:10px}
  .ml3{margin-left:15px}
  .ml4{margin-left:20px}
  .ml1{margin-left:5px}
  .ml2{margin-left:10px}
  .ml3{margin-left:15px}
  .ml4{margin-left:20px}

  /* ================================================ */
  /* PADDINGS */
  /* ================================================ */
  .p0{padding: 0}
  .p1{padding: 5px}
  .p2{padding: 10px}
  .p3{padding: 15px}
  .p4{padding: 20px}

  .pb1{padding-bottom:5px}
  .pb2{padding-bottom:10px}
  .pb3{padding-bottom:15px}
  .pb4{padding-bottom:20px}
  .pt1{padding-top:5px}
  .pt2{padding-top:10px}
  .pt3{padding-top:15px}
  .pt4{padding-top:20px}
  .pl1{padding-left:5px}
  .pl2{padding-left:10px}
  .pl3{padding-left:15px}
  .pl4{padding-left:20px}
  .pl1{padding-left:5px}
  .pl2{padding-left:10px}
  .pl3{padding-left:15px}
  .pl4{padding-left:20px}

  /* ================================================ */
  /* ALIGNMENT */
  /* ================================================ */
  .kiri, .left {text-align: left}
  .kanan, .right {text-align: right}
  .tengah, .center {text-align: center}


  /* ================================================ */
  /* FLEX */
  /* ================================================ */
  .flexy{
    display:flex;
    flex-wrap: wrap;
    gap: 15px;
  }
</style>