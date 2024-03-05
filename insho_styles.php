<style>
  /* ================================================ */
  /* Revisions: 
      v.1.0.6 - log class 
      v.1.0.5 - add sub_form class
      v.1.0.4 - remove unnecessary classes
  */
  /* ================================================ */
  a {
    text-decoration: none
  }

  .consolas {
    font-family: consolas !important
  }

  .log {
    font-family: consolas;
    font-size: 12px;
    background: yellow;
  }

  /* ================================================ */
  /* JUDUL */
  /* ================================================ */
  /* Sub Form :: Penanda Sub Form atau Nama File */
  .sub_form,
  .hasil_ajax {
    font-family: consolas;
    font-size: 10px;
    margin-bottom: 5px;
    color: #aaa;
  }

  /* ================================================ */
  /* DEBUG */
  /* ================================================ */
  .hideit {
    display: none
  }

  .debug {
    color: darkred;
    background: yellow;
    font-family: consolas;
    font-size: small;
    padding: 5px;
  }

  /* ================================================ */
  /* COLORING */
  /* ================================================ */
  .transparan {
    border: none;
    background: none
  }

  .biru,
  .blue {
    color: blue
  }

  .merah,
  .red {
    color: red
  }

  .hijau,
  .green {
    color: green
  }

  .kuning,
  .yellow {
    color: orange
  }

  .ungu,
  .purple {
    color: purple
  }

  .coklat,
  .brown {
    color: brown
  }

  .darkblue {
    color: darkblue
  }

  .darkred {
    color: darkred
  }

  .putih,
  .white {
    color: white
  }

  .hitam,
  .black {
    color: black
  }

  .darkabu {
    color: #555
  }

  .abu,
  .gray {
    color: gray
  }

  .lightabu {
    color: #aaa
  }


  /* ================================================ */
  /* BACKGROUND */
  /* ================================================ */
  .gradasi-abu {
    background: linear-gradient(#fff, #eee)
  }

  .gradasi-biru {
    background: linear-gradient(#eef, #ccf)
  }

  .gradasi-toska {
    background: linear-gradient(#eff, #cff)
  }

  .gradasi-hijau {
    background: linear-gradient(#efe, #cfc)
  }

  .gradasi-merah {
    background: linear-gradient(#fee, #fcc)
  }

  .gradasi-kuning {
    background: linear-gradient(#ffe, #ffc)
  }

  .gradasi-pink {
    background: linear-gradient(#fdf, #faf)
  }

  .gradasi-tulang {
    background: linear-gradient(#ffe, rgb(213, 234, 233))
  }

  .bg-white,
  .bg-putih {
    background: white;
  }

  .bg-red {
    background: red;
  }

  .bg-blue {
    background: blue;
  }

  .bg-yellow {
    background: yellow;
  }

  .bg-purple {
    background: purple;
  }

  .bg-black {
    background: black;
  }

  /* ================================================ */
  /* TEXT-STYLING */
  /* ================================================ */
  .tebal,
  .bold {
    font-weight: bold
  }

  .small,
  .kecil {
    font-size: small
  }

  .big,
  .besar {
    font-size: 24px
  }

  .system {
    font-family: consolas
  }

  .upper {
    text-transform: uppercase
  }

  .lower {
    text-transform: lowercase
  }

  .proper {
    text-transform: capitalize
  }

  .miring {
    font-style: italic
  }

  /* ================================================ */
  /* BOX & NAVIGATION */
  /* ================================================ */
  .wadah {
    border: solid 1px #ccc;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 15px;
  }

  .wadah_active,
  .wadah-active {
    border: solid 3px blue;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 15px;
    background: linear-gradient(#efe, #cfc);
  }

  .tr_active,
  .tr-active {
    border: solid 4px blue;
    font-weight: bold;
  }

  .navigasi a {
    display: inline-block;
    padding: 5px 15px;
    background: linear-gradient(#eef, #cfc);
    min-width: 50px;
    text-align: center;
    border-radius: 10px;
    transition: .2s
  }

  .navigasi a:hover {
    background: linear-gradient(#eef, #fcf);
    letter-spacing: 1px;
    text-transform: uppercase
  }

  /* ================================================ */
  /* FONT CASE */
  /* ================================================ */
  .lower {
    text-transform: lowercase
  }

  .upper {
    text-transform: uppercase
  }

  .kapital,
  .proper {
    text-transform: capitalize
  }

  /* ================================================ */
  /* FORM & TABLE */
  /* ================================================ */
  .form-group {
    margin-top: 10px
  }

  .btn-block {
    width: 100%
  }

  .form-ket,
  .input-keterangan {
    font-size: small;
    color: gray;
    margin: 10px 0 10px 10px
  }

  /* ================================================ */
  /* EDITABLE */
  /* ================================================ */
  .editable {
    background: linear-gradient(#efe, #cfc);
    cursor: pointer;
    transition: .2s
  }

  .editable:hover {
    background: linear-gradient(#fef, #fcf);
    letter-spacing: 1px
  }

  .deletable {
    background: linear-gradient(#fee, #fcc);
    cursor: pointer;
    transition: .2s
  }

  .deletable:hover {
    background: linear-gradient(#fef, #fcf);
    letter-spacing: 1px
  }

  /* ================================================ */
  /* ANIMATED */
  /* ================================================ */
  .pointer,
  .closable {
    cursor: pointer;
  }

  .zoom,
  .img_zoom,
  .img-zoom {
    transition: .2s
  }

  .zoom:hover,
  .img_zoom:hover,
  .img-zoom:hover {
    transform: scale(1.2);
  }

  /* ================================================ */
  /* MARGINS */
  /* ================================================ */
  .m0 {
    margin: 0
  }

  .m1 {
    margin: 5px
  }

  .m2 {
    margin: 10px
  }

  .m3 {
    margin: 15px
  }

  .m4 {
    margin: 20px
  }

  .mb1 {
    margin-bottom: 5px
  }

  .mb2 {
    margin-bottom: 10px
  }

  .mb3 {
    margin-bottom: 15px
  }

  .mb4 {
    margin-bottom: 20px
  }

  .mt1 {
    margin-top: 5px
  }

  .mt2 {
    margin-top: 10px
  }

  .mt3 {
    margin-top: 15px
  }

  .mt4 {
    margin-top: 20px
  }

  .ml1 {
    margin-left: 5px
  }

  .ml2 {
    margin-left: 10px
  }

  .ml3 {
    margin-left: 15px
  }

  .ml4 {
    margin-left: 20px
  }

  .mr1 {
    margin-right: 5px
  }

  .mr2 {
    margin-right: 10px
  }

  .mr3 {
    margin-right: 15px
  }

  .mr4 {
    margin-right: 20px
  }

  /* ================================================ */
  /* PADDINGS */
  /* ================================================ */
  .p0 {
    padding: 0
  }

  .p1 {
    padding: 5px
  }

  .p2 {
    padding: 10px
  }

  .p3 {
    padding: 15px
  }

  .p4 {
    padding: 20px
  }

  .pb1 {
    padding-bottom: 5px
  }

  .pb2 {
    padding-bottom: 10px
  }

  .pb3 {
    padding-bottom: 15px
  }

  .pb4 {
    padding-bottom: 20px
  }

  .pt1 {
    padding-top: 5px
  }

  .pt2 {
    padding-top: 10px
  }

  .pt3 {
    padding-top: 15px
  }

  .pt4 {
    padding-top: 20px
  }

  .pl1 {
    padding-left: 5px
  }

  .pl2 {
    padding-left: 10px
  }

  .pl3 {
    padding-left: 15px
  }

  .pl4 {
    padding-left: 20px
  }

  .pr1 {
    padding-right: 5px
  }

  .pr2 {
    padding-right: 10px
  }

  .pr3 {
    padding-right: 15px
  }

  .pr4 {
    padding-right: 20px
  }

  /* ================================================ */
  /* ALIGNMENT */
  /* ================================================ */
  .kiri,
  .left {
    text-align: left
  }

  .kanan,
  .right {
    text-align: right
  }

  .tengah,
  .center {
    text-align: center
  }


  /* ================================================ */
  /* BORDER | BORDER RADIUS */
  /* ================================================ */
  .bordered {
    border: solid 1px #ccc
  }

  .border-merah {
    border: solid 1px #f55
  }

  .border-biru {
    border: solid 1px #55f
  }

  .border-hijau {
    border: solid 1px #5f5
  }

  .br50 {
    border-radius: 50%
  }

  .br5 {
    border-radius: 5px
  }

  .br6 {
    border-radius: 6px
  }

  .br7 {
    border-radius: 7px
  }

  .br8 {
    border-radius: 8px
  }

  .br9 {
    border-radius: 9px
  }

  .br10 {
    border-radius: 10px
  }

  .br11 {
    border-radius: 11px
  }

  .br12 {
    border-radius: 12px
  }

  .br13 {
    border-radius: 13px
  }

  .br14 {
    border-radius: 14px
  }

  .br15 {
    border-radius: 15px
  }


  .f8 {
    font-size: 8px
  }

  .f9 {
    font-size: 9px
  }

  .f10 {
    font-size: 10px
  }

  .f11 {
    font-size: 11px
  }

  .f12 {
    font-size: 12px
  }

  .f14 {
    font-size: 14px
  }

  .f16 {
    font-size: 16px
  }

  .f18 {
    font-size: 18px
  }

  .f20 {
    font-size: 20px
  }

  .f22 {
    font-size: 22px
  }

  .f24 {
    font-size: 24px
  }

  .f26 {
    font-size: 26px
  }

  .f28 {
    font-size: 28px
  }

  .f30 {
    font-size: 30px
  }

  .f35 {
    font-size: 35px
  }

  .f40 {
    font-size: 40px
  }

  .f45 {
    font-size: 45px
  }

  .f50 {
    font-size: 50px
  }

  .f55 {
    font-size: 55px
  }

  .f60 {
    font-size: 60px
  }

  .f65 {
    font-size: 65px
  }

  .f70 {
    font-size: 70px
  }

  .f75 {
    font-size: 75px
  }

  .f80 {
    font-size: 80px
  }

  .f85 {
    font-size: 85px
  }

  .f90 {
    font-size: 90px
  }

  .f95 {
    font-size: 95px
  }

  .f100 {
    font-size: 100px
  }


  /* ================================================ */
  /* FLEX */
  /* ================================================ */
  .flexy {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .flex-between {
    display: flex;
    justify-content: space-between;
  }

  .grid2 {
    display: grid;
    grid-template-columns: auto auto;
  }

  .grid3 {
    display: grid;
    grid-template-columns: auto auto auto;
  }

  .grid4 {
    display: grid;
    grid-template-columns: auto auto auto auto;
  }

  .grid5 {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
  }

  .grid6 {
    display: grid;
    grid-template-columns: auto auto auto auto auto auto;
  }
</style>