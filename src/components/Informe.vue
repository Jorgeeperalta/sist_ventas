<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Informe</h1></v-label>
      <br />
      <v-label
        ><h3 style="margin-left: 10px">
          Filtrar por Apellido ó Fecha ó Tipo de Pago ó Ambos
        </h3></v-label
      >
      <v-row>
        <v-col md="3" style="margin-top: 30px">
          <v-text-field
            label="Ingrese numero de remito"
            v-model="numeroremito"
            outlined
          ></v-text-field>
        </v-col>
        <v-col md="3">
          <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="value"
            :items="clientes"
            item-text="apellido"
            item-value="apellido"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
        </v-col>
        <v-col md="3" style="margin-top: 30px">
          <date-picker
            label="Fecha"
            v-model="time1"
            valueType="format"
          ></date-picker
        ></v-col>

        <v-col md="3" style="margin-top: 30px">
          <v-btn color="green" @click="filtar()" outlined>Buscar</v-btn>
          <v-btn
            style="margin-left: 30px"
            color="red"
            @click="limpiar()"
            outlined
            >Limpiar</v-btn
          >
        </v-col>
      </v-row>
      <v-label v-if="totalventa != 0"
        ><h3 style="margin-left: 10px">Total: {{ totalventa }}</h3></v-label
      >
      <v-col md="12">
        <v-btn outlined color="green" @click="tirarcaja()">Caja</v-btn>
        <br /><br />
        <v-label style="margin-left: 150px" v-if="Efectivo != 0"
          >Efectivo $:{{ Efectivo }}___ Cheque: {{ Cheque }}__ Cta. DNI:
          {{ CtaDNI }}__ Mercado Pago: {{ MercadoPago }}__ Transferencia:
          {{ Transferencia }}__ Targeta: {{ Targeta }}</v-label
        >
      </v-col>

      <v-data-table
        :headers="headers"
        :items="tableventas"
        :items-per-page="5"
        class="elevation-1"
      >
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="amplia(item)"> mdi-pencil </v-icon>
          <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
        </template>
      </v-data-table>
      <v-dialog v-model="dialog" width="500px">
        <v-card height="720px">
          <br />
          <div>
            <div style="margin-left: 350px">
              <v-label>Fecha: {{ detalle.fecha }} <br /> </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Cliente: {{ detalle.apellido }} <br /> </v-label>
            </div>
            <br />
            <div style="margin-left: 50px" v-if="detalle.tipoventa == 'Acopio'">
              <v-label
                >Estado: {{ detalle.tipoventa }} <br />
                Fecha Estimada de retiro {{ detalle.fecharetiro }} <br />
              </v-label>
            </div>
            <div v-else style="margin-left: 50px">
              <v-label
                >Estado: {{ detalle.tipoventa }} <br />
                Fecha de salida: {{ detalle.fechasalida }}
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Articulos <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="art in strarts" :key="art">
                {{ art }} <br />
              </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Tipo de Pago: {{ detalle.tipopago }} <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Forma/s de pago <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="mixto in datosm" :key="mixto">
                {{ mixto.tipo }} {{ mixto.pesos }} <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label
                >Total: $ <b> {{ detalle.monto }} </b> <br />
              </v-label>
            </div>
            <br />
            <div style="margin-left: 50px" v-if="detalle.tipoventa == 'Acopio'">
              <v-icon
                @click="(dialogacopio = true), filtradetalle(detalle.id)"
                color="red"
                >mdi-search-web</v-icon
              >
              <v-row>
                <v-col md="4"
                  ><v-text-field
                    outlined
                    type="number"
                    color="green"
                    label="Id articulo"
                    v-model="detalleacopio"
                  ></v-text-field
                ></v-col>
                <v-col md="4"
                  ><v-text-field
                    type="number"
                    outlined
                    color="green"
                    label="Cantidad"
                    v-model="cantidad"
                  ></v-text-field
                ></v-col>
                <v-col md="4">
                  <v-btn
                    style="margin-left: 10px"
                    outlined
                    color="green"
                    @click="agregaitems(detalle.id, detalleacopio, cantidad)"
                    >+</v-btn
                  >
                </v-col>
              </v-row>
              <v-row>
                <v-list>
                  <v-list-item
                    v-for="(array, index) in arrayarticulos"
                    :key="index"
                  >
                    {{ array.cantidad }} {{ array.nombre }}
                    <v-btn
                      style="margin-left: 200px"
                      outlined
                      @click="eliminaitem(array)"
                      color="red"
                      >X</v-btn
                    >
                  </v-list-item>
                </v-list>
              </v-row>

              <v-btn
                style="margin-left: 10px"
                outlined
                color="green"
                @click="descuentaacopio(detalle.id)"
                >Guardar</v-btn
              >
              <v-btn
                style="margin-left: 200px"
                outlined
                color="red"
                @click="actualizaestado(detalle.id)"
                >Retirado</v-btn
              >
            </div>
          </div>
        </v-card>
      </v-dialog>
      <v-dialog v-model="dialogacopio" width="500px">
        <v-card height="600px">
          <h3 style="margin-left: 30px">Apto para retiro 'A fovor del Cliente'</h3>
          <br>
          <div style="margin-left: 50px">
            <v-label v-for="art in dataacopio" :key="art">
              <div v-if="art.cantidad > 0">
                {{ art.cantidad }}___ {{ art.detalle }}___ {{ art.fecha }}<br />
              </div>
            </v-label>
          </div>
        </v-card>
      </v-dialog>
    </v-card>
  </v-app>
</template>
<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import imagen from "@/imagen"
export default {
  components: { DatePicker },
  data() {
    return {
      dataacopio: [],
      cantidad: 0,
      dialogacopio: false,
      detalleacopio: "",
      Efectivo: 0,
      Targeta: 0,
      Cheque: 0,
      CtaDNI: 0,
      MercadoPago: 0,
      Transferencia: 0,
      datosmixtos: [],
      datosm: [],
      detalle: [],
      tipopago: null,
      dialog: false,
      time1: null,
      aux: [],
      clientes: [],
      value: null,
      ventas: [],
      totalventa: 0,
      numeroremito: 0,
      dtacopio: [],
      tiposdepago: ["Simple", "Mixto"],
      headers: [
        { text: "ID", value: "id" },
        {
          text: "Apellido y nombre",
          align: "start",
          sortable: false,
          value: "apellido",
        },
        { text: "Fecha", value: "fecha" },
        { text: "Monto", value: "monto" },
        { text: "Tipo de pago", value: "tipopago" },
        { text: "Estado", value: "tipoventa" },
        { text: "Acciones", value: "actions", sortable: false },
      ],
      articulos: [],
      arrayarticulos: [],
      ventas: [],
      tableventas: [],
      strarts: [],
    };
  },
//ALTER TABLE users MODIFY COLUMN ID_coins DECIMAL(4, 2);
  created() {
    this.cargaclientes();
    this.cargaventas();
    this.cargamixtos();
    this.cargadetalleacopio();
    fetch("http://jorgeperalta-001-site6.itempurl.com/articulos.php")
      .then((res) => res.clone().json())
      .then((res) => {
        this.articulos = res;
      })
      .catch((err) => {
        console.log(err);
      })
      .finally(() => (this.isLoading = false));
  },
  methods: {
      print() {
    //  console.log(this.salidapost);
      //this.numerodeventa=this.salidapost.idventa;
      var horaA = new Date().toISOString().substr(0, 10);
      var arrayFeha = horaA.split(["-"]);
      let pdfName = "Factura ";
      var contador = 20;
      var doc = new jsPDF();
      doc.setLineWidth(1.5);
      doc.line(8, 5, 200, 5);
      var image =imagen;
      doc.addImage(image, "PNG", 85, 6, 40, 40);
      doc.line(10, 35, 200, 35);
      doc.setFontSize(14);
      doc.text(
        150,
        12,
        "Fecha:  " + arrayFeha[2] + "-" + arrayFeha[1] + "-" + arrayFeha[0]
      );
      if (this.presupuesto == true) {
        doc.setFontSize(14);
        doc.text(10, 12, "Presupuesto");
      } else {
        doc.text(10, 12, "Remito:  " + this.salidapost.idventa);
      }
      doc.setFontSize(8);
      doc.text(150, 30, "Uso interno");
      doc.setFontSize(13);
      doc.text(10, 47, "Condicion de IVA : Consumidor Final  ");
      doc.setFontSize(13);
      if (this.presupuesto != true) {
        doc.text(130, 47, "Tipo de Pago:  " + this.opcion);
      }
      doc.setFontSize(13);
      doc.text(10, 57, "Apellido y Nombre:  " + this.customer.apellido);
      doc.setFontSize(13);
      doc.text(130, 57, "Domicilio:  " + this.customer.domicilio);
      doc.setFontSize(13);
      doc.text(10, 67, "Localidad:  " + this.customer.localidad);
      doc.setFontSize(13);
      if (this.presupuesto == true) {
        doc.text(130, 67, "Presupuesto");
      } else {
        doc.text(130, 67, "Retira: " + this.auxtipoventa);
      }

      if (this.auxtipoventa == "Acopio") {
        doc.setFontSize(13);
        doc.setTextColor("red");
        doc.text(165, 67, " " + this.auxfecharetiro);
      }
      doc.setTextColor(0, 0, 0);
      doc.setLineWidth(0.8);
      doc.line(10, 75, 200, 75);
      doc.setLineWidth(0.6);
      doc.line(10, 78, 200, 78);
      doc.setFontSize(9);
      doc.text(10, 85, "Cantidad ");
      doc.setLineWidth(0.6);
      doc.text(30, 85, "Articulo ");
      doc.setLineWidth(0.6);
      doc.text(100, 85, "Precio U.");
      doc.setLineWidth(0.6);
      doc.text(170, 85, "Sub Total ");

      doc.setLineWidth(0.6);
      doc.line(10, 88, 200, 88);

      var contador = 95;

      this.productos.forEach((element) => {
        console.log(element);
        //    doc.setFontSize(9);
        doc.text(10, contador, "  " + element.cantidad);
        doc.setLineWidth(0.6);
        doc.text(30, contador, " " + element.nombre);
        doc.setLineWidth(0.6);
        if (this.auxtipoventa == "cta cte") {
          doc.text(100, contador, "");
          doc.setLineWidth(0.6);
          doc.text(170, contador, "");
        } else {
          doc.text(100, contador, "" + element.precio);
          doc.setLineWidth(0.6);
          doc.text(170, contador, "" + element.sub_total);
        }

        contador = contador + 10;
      });
      if (this.auxtipoventa == "cta cte") {
        contador = contador + 20;
        doc.setDrawColor(255, 0, 0);
        doc.setLineWidth(0.4);
        doc.line(10, contador - 10, 200, contador - 10);
        doc.text(90, contador - 7, " Articulos Pagados");
        this.productoscta.forEach((element) => {
          console.log(element);
          //    doc.setFontSize(9);
          doc.text(10, contador, "  " + element.cantidad);
          doc.setLineWidth(0.6);
          doc.text(30, contador, " " + element.nombre);
          doc.setLineWidth(0.6);

          doc.text(100, contador, "" + element.precio);
          doc.setLineWidth(0.6);
          doc.text(170, contador, "" + element.sub_total);

          contador = contador + 10;
        });
      }
      contador = contador + 10;

      doc.setFontSize(14);
      if (this.presupuesto != true) {
        if (this.auxtipoventa == "cta cte") {
          doc.text(10, contador, "Total $  " + Math.round(this.montocta));
          contador = contador + 10;
        } else {
          doc.text(10, contador, "Total $  " + Math.round(this.monto));
          contador = contador + 10;
        }

        doc.text(10, contador, "Entrego $  " + this.efectivo);
        contador = contador + 10;
        doc.text(10, contador, "Su vuelto $  " + Math.round(this.fin));
      } else {
        doc.text(
          10,
          contador,
          "Los precios estan sujetos a cambio sin previo aviso"
        );
      }

      doc.save(pdfName + ".pdf");
    },
    eliminaitem(articulo) {
      const index = this.arrayarticulos.indexOf(articulo);

      confirm("Esta seguro de eliminar este item?") &&
        this.arrayarticulos.splice(index, 1);
    },
    agregaitems(pk, idarticulo, cant) {
      this.articulos.forEach((el) => {
        if (el.id == idarticulo) {
          this.arrayarticulos.push({
            fkventa: pk,
            pkarticulo: idarticulo,
            cantidad: cant,
            nombre: el.nombre,
          });
        }
      });
    },
    descuentaacopio(fk) {
      // alert(this.montodeuda + "" + fk);
      this.arrayarticulos.forEach((element) => {
        // console.log(element )
        console.log(fk);
        var formdata = new FormData();

        var requestOptions = {
          method: "PUT",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/detalleacopio.php?id=" +
            element.fkventa +
            "&cantidad=" +
            element.cantidad +
            "&nombre=" +
            element.nombre +
            "&fecha=" +
            new Date().toISOString().substr(0, 10) +
            "",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result))
          .then((result) => console.log("Se actualizo con exito!!"))
          .catch((error) => console.log("error", error));
      });
      this.restastock();
      alert("Se actualizo con exito!!");
    },
    restastock() {
      this.arrayarticulos.forEach((element) => {
        var formdata = new FormData();

        formdata.append("id", element.pkarticulo);
        formdata.append("resta", element.cantidad);

        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/actualizastock.php",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));
      });
    },
    cargamixtos() {
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/mixto.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.datosmixtos = data;

        console.log(this.datosmixtos);
      });
    },

    actualizaestado(pk) {
      var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" +
          pk +
          "&tipoventa=" +
          "Retirado" +
          "&fechasalida=" +
          new Date().toISOString().substr(0, 10) +
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));
    },
    deleteItem(item) {
      //   console.log(item);
      const index = this.tableventas.indexOf(item);

      var option = confirm("Desea eliminar esta venta! \n  OK or Cancelar.");
      if (option) {
        var formdata = new FormData();

        var requestOptions = {
          method: "DELETE",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" + item.id,
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result), this.cargaventas())

          .catch((error) => console.log("error", error));
      }
    },
    amplia(arts) {
      this.datosm = [];
      //   console.log(arts.id);
      this.datosmixtos.forEach((element) => {
        if (parseInt(arts.id) == parseInt(element.fkventa)) {
          this.datosm.push(element);
        }
      });

      this.detalle = arts;
      var aux = this.detalle.strarticulos;
      this.strarts = aux.split(["-"]);

      this.dialog = true;
    },
    limpiar() {
      this.tableventas = [];
      this.time1 = null;
      this.value = null;
      this.totalventa = 0;
      this.aux = [];
      this.tipopago = null;
      this.Efectivo = 0;
      this.Targeta = 0;
      this.Cheque = 0;
      this.CtaDNI = 0;
      this.MercadoPago = 0;
      this.Transferencia = 0;
      this.numeroremito = 0;
    },
    //  "Efectivo",
    //   "Targeta",
    //   "Cheque",
    //   "Cta. DNI",
    //   "Mercado Pago",
    //   "Transferencia",
    tirarcaja() {
      //  console.log(this.datosmixtos)
      // console.log(this.aux)
      this.aux.forEach((elementaux) => {
        this.datosmixtos.forEach((elementmixtos) => {
          if (elementmixtos.fkventa == elementaux.id) {
            if (elementmixtos.tipo == "Efectivo") {
              this.Efectivo += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Targeta") {
              this.Targeta += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Cheque") {
              this.Cheque += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Cta. DNI") {
              this.CtaDNI += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Mercado Pago") {
              this.MercadoPago += parseInt(elementmixtos.pesos);
            } else if (elementmixtos.tipo == "Transferencia") {
              this.Transferencia += parseInt(elementmixtos.pesos);
            }
            // console.log(elementmixtos.fkventa + "   " + elementaux.id)
          }
        });
      });
    },
    filtar() {
      if (this.value != null && this.time1 != null && this.tipopago != null) {
        // alert(this.value + " " + this.time1 + " " + this.tipopago)
        this.ventas.forEach((element) => {
          if (
            this.value == element.apellido &&
            this.time1 == element.fecha &&
            this.tipopago == element.tipopago &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null && this.tipopago != null) {
        this.ventas.forEach((element) => {
          if (
            this.time1 == element.fecha &&
            this.tipopago == element.tipopago &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null && this.value != null) {
        this.ventas.forEach((element) => {
          if (
            this.time1 == element.fecha &&
            this.value == element.apellido &&
            element.tipopago != "Debe"
          ) {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null) {
        this.ventas.forEach((element) => {
          if (this.time1 == element.fecha && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.value != null) {
        this.ventas.forEach((element) => {
          if (this.value == element.apellido && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.numeroremito != null) {
        this.ventas.forEach((element) => {
          if (this.numeroremito == element.id && element.tipopago != "Debe") {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else {
        confirm("No se encontraron resultados");
      }
      this.tableventas = this.aux;
      console.log(this.ventas);
    },
    cargaclientes() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/clientes.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.clientes = result))
        .catch((error) => console.log("error", error));
    },

    cargaventas() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php",
        requestOptions
      )
        .then((response) => response.json())
        //.then((result) => (console.log(result)))
        .then((result) => (this.ventas = result))
        .catch((error) => console.log("error", error));
    },
    cargadetalleacopio() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/detalleacopio.php",
        requestOptions
      )
        .then((response) => response.json())
        //.then((result) => (console.log(result)))
        .then((result) => (this.dtacopio = result))
        .catch((error) => console.log("error", error));
    },
    filtradetalle(idventa) {
      this.dataacopio = [];
      //   alert(idventa)
      var aux = new Array();
      this.dtacopio.forEach((element) => {
        if (element.fkventa == idventa) {
          // console.log(element.fkventa+'   '+idventa)
          aux.push(element);
        }
      });
      this.dataacopio = aux;
    },
  },
};
</script>