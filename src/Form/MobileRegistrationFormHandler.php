<?php

namespace App\Form;

use CustomerManagementFrameworkBundle\Model\CustomerInterface;
use Symfony\Component\Form\Form;

class MobileRegistrationFormHandler
{
    protected function getFormDataMapping(): array
    { 
        return [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'phone' => 'phone',
            'email' => 'email',
            'customertype' => 'customertype',
            // 'AgentID' => 'AgentID'
        ];
    }

    protected function getCustomerMapping(): array
    {
        $mapping = $this->getFormDataMapping();
        $mapping['password'] = 'password';

        return $mapping;
    }

    /**
     * Builds initial form data
     *
     * @param CustomerInterface $customer
     *
     * @return array
     */
    public function buildFormData(CustomerInterface $customer): array
    {
        $formData = [];
        foreach ($this->getFormDataMapping() as $formField => $customerProperty) {
            $getter = 'get' . ucfirst($customerProperty);

            $value = $customer->$getter();
            if (!$value) {
                continue;
            }

            $formData[$formField] = $value;
        }

        return $formData;
    }

    /**
     * Maps form values to customer
     *
     * @param CustomerInterface $customer
     * @param Form $form
     */
    public function updateCustomerFromForm(CustomerInterface $customer, Form $form)
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new \RuntimeException('Form must be submitted and valid to apply form data');
        }

        // Get form data, which could be an object or array depending on form configuration
        $formData = $form->getData();
        
        foreach ($this->getCustomerMapping() as $formField => $customerProperty) {
            $setter = 'set' . ucfirst($customerProperty);
            
            // Handle different formData types
            if (is_object($formData)) {
                // If formData is already the customer object, we don't need to set anything
                // As the form has already populated the customer object
                continue;
            } else {
                // Original code for array-based form data
                $value = $formData[$formField] ?? null;
                if (!$value) {
                    continue;
                }
                
                $customer->$setter($value);
            }
        }
    }
}

