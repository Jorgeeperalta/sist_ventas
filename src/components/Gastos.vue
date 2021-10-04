<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Gastos</h1></v-label>
      <br />
      <v-label><h3 style="margin-left: 10px">Ingrese Gastos</h3></v-label>
      <v-row>
        <v-col md="3" style="margin-top: 30px; margin-left: 10px">
          <v-textarea
            label="Ingrese Detalle"
            v-model="detalle"
            outlined
          ></v-textarea>
        </v-col>
        <v-col md="2">
          <v-text-field
            type="number"
            label="Ingrese Monto"
            v-model="monto"
            outlined
            style="margin-top: 125px"
          ></v-text-field>
        </v-col>
        <v-col md="3" style="margin-top: 125px">
          <v-select
            :items="tiposdepago"
            label="Tipo de pago"
            v-model="tipopago"
          ></v-select>
        </v-col>

        <v-col md="3" style="margin-top: 125px; magin-left: -10px">
          <v-btn color="green" @click="guardargasto()" outlined>Guardar</v-btn>
        </v-col>
      </v-row>
      <v-label
        ><h3 style="margin-left: 10px">Filtra Gastos por fecha</h3></v-label
      >
      <v-row>
        <v-col md="3" style="margin-top: 30px;margin-left: 10px">
          <date-picker
            label="Fecha"
            v-model="time1"
            valueType="format"
          ></date-picker
        ></v-col>
        <v-col md="3" style="margin-top: 30px">
          <v-btn color="yellow" @click="buscagasto()" outlined>Buscar</v-btn>
        </v-col>
        <v-col style="margin-top: 30px" v-if="total!=0" md="3"> <h2> Total: $ <b> {{total}} </b> </h2> </v-col>
      </v-row>
      <template>
        <v-data-table :headers="headers" :items="auxgastos" class="elevation-1">
        
          <template v-slot: [`item.monto`]="{ item }">
            <v-chip :color="getColor(item.monto)" dark>
              {{ item.monto }}
            </v-chip>
          </template>
        </v-data-table>
      </template></v-card
    >
  </v-app>
      </template>

    </v-card>
  </v-app>
</template>   
<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: { DatePicker },
  data: () => ({
    headers: [
      {
        text: "Detalle",
        align: "start",
        sortable: false,
        value: "detalle",
      },
      { text: "Monto", value: "monto" },
      { text: "tipo de pago", value: "tipopago" },
      { text: "Fcha", value: "fecha" },
    ],
    total:0,
    time1: "",
    gastos: [],
    auxgastos: [],
    detalle: "",
    monto: "",
    tipopago: "",
    fecha: new Date().toISOString().substr(0, 10),
    salidapost: [],
    tiposdepago: [
      "Efectivo",
      "Targeta",
      "Cheque",
      "Cta. DNI",
      "Mercado Pago",
      "Transferencia",
    ],
  }),
  mounted() {

      this.traergastos();
  },

  methods: {
    buscagasto() {
      this.auxgastos=[]
      this.total=0
      this.gastos.forEach(element=>{
            if(element.fecha==this.time1){
              this.auxgastos.push(element)
              this.total+=parseInt( element.monto)
            }

      })


    },
    getColor(monto) {
      if (monto > 400) return "red";
      else if (monto > 200) return "orange";
      else return "green";
    },
    traergastos() {
      var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/gastos.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.gastos = data;
        this.auxgastos=this.gastos
        console.log(data);
      });
    },
    guardargasto() {
      var formdata = new FormData();

      formdata.append("id", "");
      formdata.append("detalle", this.detalle);
      formdata.append("monto", this.monto);
      formdata.append("tipopago", this.tipopago);
      formdata.append("fecha", new Date().toISOString().substr(0, 10));

      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/gastos.php",
          { method: "POST", body: formdata }
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.salidapost = data;
        alert("Se almaceno con exito!!");
        this.limpiar();
        this.traergastos();
      });
      result.catch((error) => {
        alert("error de red " + error);
      });
    },
    limpiar() {
      this.monto = 0;
      this.detalle = "";
      this.tipopago = "";
    },
  },
};
</script>
        