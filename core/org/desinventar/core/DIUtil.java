/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

public class DIUtil {
	public static String getErrMessage(int iErrorCode) {
		String sMsg = "";
		switch(iErrorCode) {
		case 0:
		case Constants.ERR_NO_ERROR:
			sMsg = "Sin Error";
			break;
		case Constants.ERR_NO_GEOGRAPHY:
			sMsg = "GeographyId No Encontrado";
			break;
		case Constants.ERR_NULL_ID:
			sMsg = "Elemento Sin Identificador";
			break;
		case Constants.ERR_DUPLICATED_ID:
			sMsg = "Id Duplicado";
			break;
		case Constants.ERR_NO_REF:
			sMsg = "No se encontro objeto referenciado";
			break;
		case Constants.ERR_CAUSE_NULL_ID:
			sMsg = "Causa sin Identificador";
			break;
		case Constants.ERR_CAUSE_DUPLICATED_ID:
			sMsg = "Causa con Id Duplicado";
			break;
		case Constants.ERR_CAUSE_CANNOT_DELETE:
			sMsg = "Causa con Fichas Asociadas";
			break;
		case Constants.ERR_EVENT_NULL_ID:
			sMsg = "Event con Id nulo";
			break;
		case Constants.ERR_EVENT_NULL_NAME:
			sMsg = "Evento No Definido";
			break;
		case Constants.ERR_EVENT_DUPLICATED_ID:
			sMsg = "Evento con Id Duplicado";
			break;
		case Constants.ERR_EVENT_DUPLICATED_NAME:
		    sMsg = "Evento con Nombre Duplicado";
		    break;
		case Constants.ERR_EVENT_CANNOT_DELETE:
			sMsg = "No se puede borrar Evento";
			break;
		case Constants.ERR_GEOLEVEL_NULL_ID:
			sMsg = "Nivel de Geografía sin Id";
			break;
		case Constants.ERR_GEOLEVEL_DUPLICATED_ID:
			sMsg = "Nivel de Geografía con Id Duplicado";
			break;
		case Constants.ERR_GEOLEVEL_NULL_NAME:
			sMsg = "Nivel de Geografía - Nombre en blanco";
			break;
		case Constants.ERR_GEOLEVEL_DUPLICATED_NAME:
			sMsg = "Nivel de Geografía - Nombre Duplicado";
			break;
		case Constants.ERR_GEOLEVEL_DUPLICATED_LEVEL:
			sMsg = "Nivel de Geografía - Número de Nivel Duplicado";
			break;
		case Constants.ERR_GEOGRAPHY_NULL_ID:
			sMsg = "Unidad Geográfica sin Identificador";
			break;
		case Constants.ERR_GEOGRAPHY_DUPLICATED_ID:
			sMsg = "Unidad Geográfica con Id duplicado";
			break;
		case Constants.ERR_GEOGRAPHY_NULL_NAME:
			sMsg = "Unidad Geográfica sin Nombre";
			break;
		case Constants.ERR_GEOGRAPHY_NULL_LEVEL:
			sMsg = "Unidad Geográfica sin Nivel asignado";
			break;
		case Constants.ERR_DISASTER_NULL_ID:
			sMsg = "Desastre - Identificador Nulo";
			break;
		case Constants.ERR_DISASTER_DUPLICATED_ID:
			sMsg = "Desastre - Id Duplicado";
			break;
		case Constants.ERR_DISASTER_NULL_SERIAL:
			sMsg = "Desastre - Serial Nulo";
			break;
		case Constants.ERR_DISASTER_DUPLICATED_SERIAL:
			sMsg = "Desastre - Serial Duplicado";
			break;
		case Constants.ERR_DISASTER_NULL_TIME:
			sMsg = "Desastre - Fecha del Desaster no especificada";
			break;
		case Constants.ERR_DISASTER_NULL_SOURCE:
			sMsg = "Desastre - No tiene fuente de datos";
			break;
		case Constants.ERR_DISASTER_NULL_STATUS:
			sMsg = "Desastre - Estado de registro no encontrado";
			break;
		case Constants.ERR_DISASTER_NULL_CREATION:
			sMsg = "Desastre - No tiene fecha de creación";
			break;
		case Constants.ERR_DISASTER_NULL_LASTUPDATE:
			sMsg = "Desastre - no tiene fecha de actualización";
			break;
		case Constants.ERR_DISASTER_NO_GEOGRAPHY:
			sMsg = "Desastre - no hay geografia asociada";
			break;
		case Constants.ERR_DISASTER_NO_EVENT:
			sMsg = "Desastre - no hay evento asociado";
			break;
		case Constants.ERR_DISASTER_NO_CAUSE:
			sMsg = "Desastre - no hay causa asociada";
			break;
		case Constants.ERR_DISASTER_NO_EFFECTS:
			sMsg = "Registro sin Efectos";
			break;
		default:
			sMsg = "Error Desconocido";
			break;
		}
		return sMsg;
	}

	public static String getDateDisaster(String sMyDateStr) {
		String sDateStr = "";
		String sValue = "";
		int iYear;
		// error in date
		if (sMyDateStr.length() == 0)
			sMyDateStr = "0000-00-00";
		String[] sFields = sMyDateStr.split("-");
		int iCount = sFields.length;
		if (iCount > 0) {
			sValue = sFields[0].trim();
			iYear = Integer.valueOf(sValue).intValue();
			if (iYear < 70) {
				iYear = iYear + 2000;
			}
			sValue = "0000" + iYear;
			sValue = sValue.substring(sValue.length() - 4, sValue.length());
			sDateStr = sValue;
			
			sValue = "00";
			if (iCount > 1) {
				sValue = "00" + sFields[1].trim();
				sValue = sValue.substring(sValue.length() - 2, sValue.length());
			}
			sDateStr = sDateStr + "-" + sValue;

			sValue = "00";
			if (iCount > 2) {
				sValue = "00" + sFields[2].trim();
				sValue = sValue.substring(sValue.length() - 2, sValue.length());
			}
			sDateStr = sDateStr + "-" + sValue;
		}
		
		return sDateStr;
	}
} // DIUtil
