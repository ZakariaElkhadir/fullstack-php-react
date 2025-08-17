import { ApolloClient, InMemoryCache, createHttpLink } from "@apollo/client";

const httpLink = createHttpLink({
  uri: "http://localhost:8000/graphql",
  fetch: async (uri, options) => {
    const response = await fetch(uri, options);
    const text = await response.text();

    // Extract JSON from response that may contain extra output
    let jsonText = text;

    // Find the first occurrence of { which should be the start of JSON
    const jsonStart = text.indexOf("{");
    if (jsonStart > 0) {
      jsonText = text.substring(jsonStart);
    }

    return new Response(jsonText, {
      status: response.status,
      statusText: response.statusText,
      headers: response.headers,
    });
  },
});

const client = new ApolloClient({
  link: httpLink,
  cache: new InMemoryCache(),
});

export default client;
