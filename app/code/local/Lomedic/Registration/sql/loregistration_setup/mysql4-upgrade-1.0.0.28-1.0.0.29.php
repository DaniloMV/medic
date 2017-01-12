<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();


$cmsPageData = array(
    'title' => 'Privacy Policy',
    'root_template' => 'one_column',
    'meta_keywords' => 'privacy,policy',
    'meta_description' => 'privacy policy',
    'identifier' => 'privacy-policy',
    'content_heading' => 'Privacy Policy',
    'stores' => array(0),
    'content' => "Medic Joint SA de CV (en lo sucesivo “MedicJoint”), en cumplimiento por lo establecido por la Ley Federal de Protección de Datos Personales en Posesión de Particulares, hace de su conocimiento la política de privacidad y manejo de datos personales, en la que en todo momento buscará que el tratamiento de los mismos sea legítimo, controlado e informado, a efecto de asegurar la privacidad, confidencialidad, integridad y el derecho a la autodeterminación informativa de sus datos.
                “MedicJoint” sólo obtiene los datos personales de sus titulares, ya sea porque se tiene una relación jurídica con dicho titular o bien exista la posibilidad de tener dicha relación jurídica de manera directa y personal, o bien a través de los medios electrónicos, ópticos, sonoros, visuales o por cualquier otra tecnología u otras fuentes que estén permitidas por la ley, con la finalidad de acreditar la identificación del titular de dichos datos personales de conformidad con las leyes y disposiciones aplicables y con el único propósito de estar en posibilidades de celebrar el contrato que en su caso y por acuerdo mutuo se pretenda formalizar, para mantener y custodiar el expediente e información respectiva.
                Asimismo, “MedicJoint” podrá usar la información de los titulares de los datos personales para contactarles, entender mejor sus necesidades, recabar información y cualquier acto tendiente afín con las necesidades de los titulares y de “MedicJoint”.
                En caso de que los titulares de la información deseen ejercer sus derechos de acceso, rectificación, cancelación u oposición, éstos los podrán ejercer en todo momento, de conformidad con el mismo procedimiento para girar cualquier otra instrucción derivada de la relación jurídica con dicho titular o bien, de la posibilidad de tener dicha relación jurídica, pudiendo realizar esto a través de nuestro departamento jurídico con el Lic. Juan Carlos Tadeo Martínez García o bien, solicitando uno de los siguientes formatos al correo atencionaclientes@medicjoint.com
                Formato de tratamiento de datos personales – persona moral Formato de tratamiento de datos personales – persona física
                “MedicJoint” se compromete a que los datos personales serán tratados bajo las más estrictas medidas de seguridad que garantice su confiabilidad. “MedicJoint” se reserva el derecho de modificar y actualizar el presente aviso de privacidad en cualquier momento, para la atención, adecuación y cumplimiento de las modificaciones legales, que en su caso, sean aplicables, políticas internas o nuevos requerimientos para la prestación u ofrecimiento de nuestros servicios o productos y lo mantendrá siempre a disposición en este mismo medio para su consulta."
);

Mage::getModel('cms/page')->setData($cmsPageData)->save();

$cmsPageData = array(
    'title' => 'About us',
    'root_template' => 'one_column',
    'meta_keywords' => 'about,us',
    'meta_description' => 'about us',
    'identifier' => 'about-us',
    'content_heading' => 'About us',
    'stores' => array(0),
    'content' => "About us"
);

Mage::getModel('cms/page')->setData($cmsPageData)->save();

$cmsPageData = array(
    'title' => 'About us',
    'root_template' => 'one_column',
    'meta_keywords' => 'about,us',
    'meta_description' => 'about us',
    'identifier' => 'about-us',
    'content_heading' => 'About us',
    'stores' => array(0),
    'content' => "About us"
);

Mage::getModel('cms/page')->setData($cmsPageData)->save();


$installer->endSetup();