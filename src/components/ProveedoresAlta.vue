<template>
  <v-app>
    <v-card class="mx-auto" width="1200">
      <v-spacer></v-spacer> <br />

      <v-label
        ><h1 style="margin-left: 10px">
          Carga Movimientos de Proveedores
        </h1></v-label
      >

      <div>
        <v-card class="d-flex flex-row mb-2" flat tile>
          <v-row>
            <v-col md="2">
              <v-autocomplete
                style="margin-left: 10px; margin-top: 30px"
                v-model="idproveedor"
                :items="proveedores"
                item-text="nombre"
                item-value="id"
                dense
                filled
                label="Buscar por apellido"
              ></v-autocomplete>
            </v-col>
            <v-col md="2">
              <v-text-field
                style="margin-top: 38px"
                type="number"
                v-model="remito"
                label="Numero de Remito"
              ></v-text-field>
            </v-col>

            <v-col md="2">
              <v-text-field
                style="margin-top: 38px"
                type="number"
                v-model="idproducto"
                label="Id Producto"
              ></v-text-field>
            </v-col>
            <v-col md="2">
              <v-text-field
                style="margin-top: 38px"
                type="number"
                v-model="cantidad"
                label="Cantidad"
              ></v-text-field>
            </v-col>
            <v-col md="2">
              <v-text-field
                style="margin-top: 38px"
                type="number"
                v-model="costo"
                label="Costo"
              ></v-text-field>
            </v-col>

            <v-col md="2">
              <v-btn
                @click="guardaralta()"
                outlined
                color="blue"
                style="margin-top: 47px"
                ><v-icon small class="mr-2"> mdi-plus </v-icon></v-btn
              >
            </v-col>
            <v-col md="10">
              <v-text-field
                style="margin-left: 11px"
                number
                v-model="detalle"
                label="detalle"
              ></v-text-field>
            </v-col>

            <v-col md="2" style="margin-top: 11px">
              <v-btn outlined color="green" @click="guardaraltaproveedor()">Guardar</v-btn>
            </v-col>
          </v-row>
        </v-card>
        <div style="margin-left: 950px"></div>
      </div>

      <template>
        <v-row>
          <v-col cols="12" sm="12">
            <v-card width="1000">
               
                <v-label   style="margin-left: 11px" v-if="total!=0"><h1>Total $ {{ total }}</h1></v-label>
              <v-data-table
                style="margin-left: 11px"
                :headers="headers"
                :items="datos"
                item-key="nombre"
                class="elevation-1"
              >
                <template v-slot:[`item.actions`]="{ item }">
                  <v-icon color="pink accent-4" small @click="deleteItem(item)">
                    mdi-delete
                  </v-icon>
                </template>
                <template v-slot:no-data>
                  <v-btn color="primary" @click="initialize">Reset</v-btn>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-card>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    unidadstockid:'',
    totalarticulos:'',
    datos: [],
    articulos: [],
    idproducto: 0,
    remito: 0,
    cantidad: 0,
    total:0,
    
    nombredearticulo: "",
    costo: 0,
    fdesde: "",
    fhasta: "",
    detalle: "",
    idproveedor:0,
    salidapost:0,
    calendario: null,
    datenow: new Date(),
    dates: ["2014-11-11", new Date().toISOString().substr(0, 10)],
    proveedores: [],
    headers: [
      { text: "Id Producto", value: "idproducto" },
      { text: "Articulo", value: "nombre" },
      { text: "Cantidad", value: "cantidad" },

      { text: "Costo", value: "costo" },
      { text: "Sub Total", value: "subtotal" },

      { text: "Actions", value: "actions", sortable: false },
    ],
  }),
  mounted() {
    this.traerproveedores();
    this.cargar_datos();
  },
  methods: {
      unidadstockmetodo() {
        this.datos.forEach((element) => {

       
      var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/actualizastock.php?id=" +
          element.idproducto +
          "&stock=" +
           element.cantidad +
          "&fecha=" +
           new Date().toISOString().substr(0, 10)+
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .then((result) => console.log('Se actulizo de exito!!'))
        .catch((error) => console.log("error", error));

         })

      
      
    },
    guardaraltaproveedor(){
         console.log(this.datos)
     // alert(this.idproveedor+'  '+this.remito+'   '+ this.totalarticulos+'    '+this.total)
           if (this.totalarticulos != "") {
        var formdata = new FormData();
//id,fkproveedor,numeroremito,totalarticulos,total,fecha
        formdata.append("id", "");
        formdata.append("fkproveedor", this.idproveedor);
        formdata.append("numeroremito", this.remito);
        formdata.append("totalarticulos", this.totalarticulos);
        formdata.append("total", this.total);
        formdata.append("fecha", new Date().toISOString().substr(0, 10));

        async function asyncData() {
         
          const response = await fetch(
            "http://jorgeperalta-001-site6.itempurl.com/altaproveedor.php",
            { method: "POST", body: formdata }
          );
          const data = await response.json();

          return data;
        }

        const result = asyncData();
        
        result.then((data) => {
          this.salidapost = data;

         // console.log(this.salidapost);
          this.guardardetalle();
         this.unidadstockmetodo()
           
        })
        result.catch(error => {alert('error de red '+error)})
      } else {
        confirm("No cuenta con articulos");
      }
    },
    guardardetalle(){
          if (this.detalle != "") {
      // alert(this.salidapost.id)
           var formdata = new FormData();

        formdata.append("id", "");
        formdata.append("fkaltaproveedor", this.salidapost.id);
        formdata.append("detalle", this.detalle);
        formdata.append("fecha", new Date().toISOString().substr(0, 10));

        async function asyncData() {
         
          const response = await fetch(
            "http://jorgeperalta-001-site6.itempurl.com/detallealta.php",
            { method: "POST", body: formdata }
          );
          const data = await response.json();

          return data;
        }

        const result = asyncData();
        
        result.then((data) => {
         
        alert('Se almaceno con exito!!')
           
        })
        result.catch(error => {alert('error de red '+error)})
      } else {
        confirm("error");
      }
    },
    initialize() {
      this.cargar_datos();
    },
    deleteItem(item) {
      const index = this.datos.indexOf(item);

      confirm("Esta seguro de eliminar este item?") &&
        this.datos.splice(index, 1);
            this.total=0
            this.totalarticulos=''
          this.datos.forEach(el =>{
        this.total+=  el.subtotal
        this.totalarticulos +=
              el.cantidad +
              "  " +
              el.nombre +
              "  " +
              el.costo +
              "  " +
              el.subtotal +
              "-" +
              "\n";
      })
    //  console.log(this.totalarticulos)
    },
    cargar_datos() {
      var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/articulos.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.articulos = data;
      //  console.log(data);
      });
    },
    traerproveedores() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/proveedores.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.proveedores = result))
        .catch((error) => console.log("error", error));
    },
    guardaralta() {
      this.totalarticulos=''
      this.articulos.forEach((element) => {
        if (element.id == this.idproducto) {
          this.nombredearticulo = element.nombre;
        }
      });

      this.datos.push({
        nombre: this.nombredearticulo,
        idproducto: this.idproducto,
        cantidad: this.cantidad,
        costo: this.costo,
        subtotal : this.cantidad * this.costo
      });
      this.idproducto=0
      this.cantidad=0
      this.costo=0
       this.total=0
       this.datos.forEach(el =>{
        this.total+=  el.subtotal
         this.totalarticulos +=
              el.cantidad +
              "  " +
              el.nombre +
              "  " +
              el.costo +
              "  " +
              el.subtotal +
              "-" +
              "\n";
      })
  //   console.log(this.totalarticulos)
    },
   
  },
};
</script>

<style>
</style>