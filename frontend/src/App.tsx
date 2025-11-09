import { createBrowserRouter, RouterProvider } from "react-router-dom";
import PlansPage from "./pages/PlansPage";
import CheckoutPage from "./pages/CheckoutPage";
import ConfirmationPage from "./pages/ConfirmationPage";

const router = createBrowserRouter([
  {
    path: "/",
    element: <PlansPage />,
  },
  {
    path: "/checkout",
    element: <CheckoutPage />,
  },
  {
    path: "/confirmation/:id", 
    element: <ConfirmationPage />,
  },
]);

function App() {
  return <RouterProvider router={router} />;
}

export default App;