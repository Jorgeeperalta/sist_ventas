<template>
  <v-app>
    <v-card class="mx-auto" width="1200">
        <v-system-bar>
          <v-label>Filtro de busqueda </v-label>
        </v-system-bar>

        <div>
          <v-card class="d-flex flex-row mb-2" flat tile>
          

            <v-col  md="3" >
               <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="pkproveedor"
            :items="proveedores"
            item-text="nombre"
            item-value="id"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
            </v-col>

            <v-col
              md="3"
              
            >
              <v-text-field
                style="margin-top: 38px"
                type="number"
                v-model=" numeroremito"
                label="Numero de Remito"
              ></v-text-field>
            </v-col>
            <v-col
              md="3"
              
            >
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="date"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dateRangeText"
                    label="Rango de fechas"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="dates" range ref="picker">
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false"
                    >Cancel</v-btn
                  >
                  <v-btn text color="primary" @click="$refs.menu.save(date)"
                    >OK</v-btn
                  >
                </v-date-picker>
              </v-menu>
            </v-col>

            <v-col md="4" style="margin-top: 11px">
              <v-btn
                outlined
                @click=" muestraproveedoresmov()"
                >Buscar</v-btn
              >

            </v-col>

          </v-card>
          <div style="margin-left: 950px"></div>
        </div>

        <template>
  <v-data-table
    :headers="headers"
    :items="auxproveedoresmovs"
    class="elevation-1"
  >
      <template v-slot:[`item.actions`]="{ item }">
      <v-icon
        small
        class="mr-2"
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click="muestraItem(item)"
      >
        mdi-search-web
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn
        color="primary"
        @click="initialize"
      >
        Reset
      </v-btn>
    </template>
  </v-data-table>
</template>

        <template>
          <v-row>
            <v-col cols="12" sm="6">
              <v-dialog v-model="dialog" width="310px">
               <v-card>
                    <div>{{totalarticulos}}</div>

                    <div v-for="detalle in detalleaux" :key="detalle">
                        {{detalle.detalle}} {{detalle.fecha}}
                    </div>

               </v-card>
              </v-dialog>
                <v-dialog max-width="500px" v-model="dialogdetalle">
                <v-card>
                  <v-card-title>
                    <label>Agrega detalle</label>
                  </v-card-title>
                  <v-card-text
                    ><v-text-field
                      v-model="detalleup"
                      label="Ingrese detalle"
                    ></v-text-field>
                    <br />
                    <v-btn
                      style="margin-left: 340px"
                      @click="agregadetalle()"
                      >Actualiza</v-btn
                    >
                  </v-card-text>
                </v-card>
              </v-dialog>
            </v-col>
          </v-row>
        </template>
      </v-card>
      </v-app>
</template>

<script>
export default {
    data: () => ({
         date:'',
        detalleup:'',
         menu:false,
         dialogdetalle:false,
         auxproveedoresmovs:[],
         detalleproveedoresmovs:[],
         detalleaux:[],
         auxdetalle:'',
         dialog:false,
         fdesde: "",
         fhasta: "",
         calendario: null,
         datenow: new Date(),
         dates: ["2021-08-11", new Date().toISOString().substr(0, 10)],
         proveedores:[],
         pkproveedor:0,
         detalle:'',
         totalarticulos:'',
         numeroremito: 0,
         proveedoresmovs:[],
          headers: [
       
        { text: 'Proveedor', value: 'nombreproveedor' },
        { text: 'N° de remito', value: 'numeroremito' },
        { text: 'Fecha', value: 'fechaaltaproveedor' },
        { text: 'Total', value: 'total' },
         { text: 'Actions', value: 'actions', sortable: false },
        
      ],

    }),
    mounted(){
      this.traerproveedores()
      this.traerproveedoresmov()
      this.detalleproveedoresmov()
    },
    computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    },
    dynamicSize() {
      return [this.iconSize, this.iconSize * 1.15];
    },
    dynamicAnchor() {
      return [this.iconSize / 2, this.iconSize * 1.15];
    },
  },

    methods:{
      agregadetalle(){
                if (this.detalleup!= "") {
      // alert(this.salidapost.id)
           var formdata = new FormData();

        formdata.append("id", "");
        formdata.append("fkaltaproveedor", this.auxdetalle);
        formdata.append("detalle", this.detalleup);
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
        confirm("No cuenta con ninguna venta");
      }
      this.dialogdetalle=false;
      },
      initialize(){

      },
        detalleproveedoresmov(){
                   var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/detallealta.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.detalleproveedoresmovs = result))
        .catch((error) => console.log("error", error));

        },
        muestraItem(item){
             this.detalleaux=[]
            this.detalleproveedoresmovs.forEach((element ) =>{
                if(item.id==element.fkaltaproveedor){
                      this.detalleaux.push(element)
                }
               
            })
            this.dialog = true;
           this.detalle= item.detalle
           this.totalarticulos= item.totalarticulos
        },
        editItem(item){
         this.dialogdetalle=true;

         this.auxdetalle= item.id
           
        },
        muestraproveedoresmov(){
           this.fdesde = this.dates[0];
      this.fhasta = this.dates[1];
           
             this.auxproveedoresmovs=[]
          this.proveedoresmovs.forEach((element =>{
                   if(this.pkproveedor==element.idproveedor && this.numeroremito==0){
                     this.auxproveedoresmovs.push(element)
                   }else if(this.numeroremito!=0 && this.numeroremito==element.numeroremito){
                          this.auxproveedoresmovs.push(element)
                   
                   }else if (element.fechaaltaproveedor > this.dates[0] && element.fechaaltaproveedor < this.dates[1]  && this.numeroremito==0 && this.pkproveedor==0) {

                            this.auxproveedoresmovs.push(element)
                   }
          }))
            //console.log(this.proveedoresmovs)
        },
        traerproveedoresmov(){
                 var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/altaproveedor.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.proveedoresmovs = result))
        .catch((error) => console.log("error", error));
        },
        traerproveedores(){
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
        }
    }

}
</script>

<style>

</style>